<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    private function instituteId(): int
    {
        return Auth::user()->institute_id;
    }

    public function index(Request $request)
    {
        $instituteId = $this->instituteId();

        $query = DB::table('users')
            ->where('users.institute_id', $instituteId)
            ->whereIn('users.role', ['owner', 'academic_head', 'admin', 'sub_admin', 'teacher', 'reception'])
            ->leftJoin('user_batch_assignments as uba', 'uba.user_id', '=', 'users.id')
            ->leftJoin('subjects as ps', 'ps.id', '=', 'users.primary_subject_id')
            ->select(
                'users.*',
                'ps.name as primary_subject_name',
                'ps.code as primary_subject_code',
                DB::raw('COUNT(DISTINCT uba.batch_id) as batch_count')
            )
            ->groupBy('users.id', 'ps.name', 'ps.code');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('users.name', 'like', $s)
                  ->orWhere('users.email', 'like', $s)
                  ->orWhere('users.username', 'like', $s);
            });
        }

        if ($request->filled('role')) {
            $query->where('users.role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('users.is_active', $request->status === 'active' ? 1 : 0);
        }

        $staff = $query->orderByDesc('users.created_at')->paginate(10)->withQueryString();

        $stats = DB::table('users')
            ->where('institute_id', $instituteId)
            ->whereIn('role', ['owner', 'academic_head', 'admin', 'sub_admin', 'teacher', 'reception'])
            ->selectRaw('COUNT(*) as total, SUM(is_active) as active_count,
                SUM(CASE WHEN role = "teacher" THEN 1 ELSE 0 END) as teacher_count,
                SUM(CASE WHEN role = "reception" THEN 1 ELSE 0 END) as reception_count')
            ->first();

        $subjects = DB::table('subjects')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        // For "Assigned to" label — use primary_subject_name (already in query above)
        // Keep subjectNames for backwards compat with assignedToLabel()
        $subjectNames = $staff->mapWithKeys(fn($m) => [$m->id => $m->primary_subject_name]);

        return view('admin.staff.index', compact('staff', 'stats', 'subjects', 'subjectNames'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'email'              => 'required|email|max:150|unique:users,email',
            'username'           => 'required|string|max:50|unique:users,username',
            'password'           => 'required|string|min:6',
            'role'               => 'required|in:owner,academic_head,admin,sub_admin,teacher,reception',
            'phone'              => 'nullable|string|max:15',
            'primary_subject_id' => 'nullable|integer|exists:subjects,id|required_if:role,teacher',
        ]);

        $data['institute_id'] = $this->instituteId();
        $data['is_active']    = true;
        $data['password']     = Hash::make($data['password']);

        $userId = DB::table('users')->insertGetId($data + ['created_at' => now(), 'updated_at' => now()]);

        // Sync user_subject_assignments for teachers
        if ($data['role'] === 'teacher' && !empty($data['primary_subject_id'])) {
            $this->syncSubjectAssignment($userId, $data['primary_subject_id']);
        }

        return redirect()->route('admin.staff')->with('success', 'Staff member created successfully.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:100',
            'email'              => 'required|email|max:150|unique:users,email,' . $id,
            'username'           => 'required|string|max:50|unique:users,username,' . $id,
            'role'               => 'required|in:owner,academic_head,admin,sub_admin,teacher,reception',
            'phone'              => 'nullable|string|max:15',
            'primary_subject_id' => 'nullable|integer|exists:subjects,id|required_if:role,teacher',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $data['password'] = Hash::make($request->password);
        }

        DB::table('users')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->update($data + ['updated_at' => now()]);

        // Sync subject assignment
        if ($data['role'] === 'teacher' && !empty($data['primary_subject_id'])) {
            $this->syncSubjectAssignment($id, $data['primary_subject_id']);
        } else {
            // Non-teacher or teacher with no subject: clear assignments
            DB::table('user_subject_assignments')->where('user_id', $id)->delete();
        }

        return redirect()->route('admin.staff')->with('success', 'Staff member updated.');
    }

    public function toggle(int $id)
    {
        $user = DB::table('users')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->first();

        if ($user) {
            DB::table('users')->where('id', $id)->update([
                'is_active'  => !$user->is_active,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.staff')->with('success', 'Staff status updated.');
    }

    public function destroy(int $id)
    {
        // Allow admin to delete any staff including owner (testing phase)
        DB::table('users')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->whereNot('id', Auth::id())   // prevent self-delete
            ->delete();

        return redirect()->route('admin.staff')->with('success', 'Staff member removed.');
    }

    private function syncSubjectAssignment(int $userId, int $subjectId): void
    {
        DB::table('user_subject_assignments')->where('user_id', $userId)->delete();
        DB::table('user_subject_assignments')->insert([
            'user_id'    => $userId,
            'subject_id' => $subjectId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
