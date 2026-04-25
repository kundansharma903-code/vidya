<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    private function instituteId(): int
    {
        return Auth::user()->institute_id;
    }

    public function index(Request $request)
    {
        $instituteId = $this->instituteId();

        $query = DB::table('students')
            ->where('students.institute_id', $instituteId)
            ->join('batches', 'batches.id', '=', 'students.batch_id')
            ->join('courses', 'courses.id', '=', 'batches.course_id')
            ->select(
                'students.*',
                'batches.name as batch_name',
                'courses.name as course_name',
                'courses.exam_type'
            );

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('students.roll_number', 'like', $s)
                  ->orWhere('students.name', 'like', $s)
                  ->orWhere('students.phone', 'like', $s);
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('students.batch_id', $request->batch_id);
        }

        if ($request->filled('course_id')) {
            $query->where('batches.course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('students.is_active', $request->status === 'active' ? 1 : 0);
        }

        $students = $query->orderByDesc('students.created_at')->paginate(10)->withQueryString();

        $stats = DB::table('students')
            ->join('batches', 'batches.id', '=', 'students.batch_id')
            ->join('courses', 'courses.id', '=', 'batches.course_id')
            ->where('students.institute_id', $instituteId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(students.is_active) as active_count,
                SUM(CASE WHEN courses.exam_type IN ("NEET","BOTH") THEN 1 ELSE 0 END) as neet_count,
                SUM(CASE WHEN courses.exam_type IN ("JEE_MAIN","JEE_ADVANCED","BOTH") THEN 1 ELSE 0 END) as jee_count
            ')
            ->first();

        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $courses = DB::table('courses')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.students.index', compact('students', 'stats', 'batches', 'courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'roll_number'       => 'required|string|max:30',
            'enrollment_number' => 'nullable|string|max:30',
            'batch_id'          => 'required|integer|exists:batches,id',
            'phone'             => 'nullable|string|max:20',
            'parent_phone'      => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:150',
            'admission_date'    => 'nullable|date',
            'father_name'       => 'nullable|string|max:100',
            'mother_name'       => 'nullable|string|max:100',
            'date_of_birth'     => 'nullable|date',
            'gender'            => 'nullable|in:M,F,O',
            'medium'            => 'nullable|in:english,hindi',
            'address'           => 'nullable|string|max:500',
        ]);

        $data['institute_id'] = $this->instituteId();
        $data['is_active']    = true;

        DB::table('students')->insert($data + ['created_at' => now(), 'updated_at' => now()]);

        return redirect()->route('admin.students')->with('success', 'Student added successfully.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'roll_number'       => 'required|string|max:30',
            'enrollment_number' => 'nullable|string|max:30',
            'batch_id'          => 'required|integer|exists:batches,id',
            'phone'             => 'nullable|string|max:20',
            'parent_phone'      => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:150',
            'admission_date'    => 'nullable|date',
            'father_name'       => 'nullable|string|max:100',
            'mother_name'       => 'nullable|string|max:100',
            'date_of_birth'     => 'nullable|date',
            'gender'            => 'nullable|in:M,F,O',
            'medium'            => 'nullable|in:english,hindi',
            'address'           => 'nullable|string|max:500',
        ]);

        DB::table('students')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->update($data + ['updated_at' => now()]);

        return redirect()->route('admin.students')->with('success', 'Student updated.');
    }

    public function toggle(int $id)
    {
        $student = DB::table('students')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->first();

        if ($student) {
            DB::table('students')->where('id', $id)->update([
                'is_active'  => !$student->is_active,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.students')->with('success', 'Student status updated.');
    }

    public function destroy(int $id)
    {
        DB::table('students')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->delete();

        return redirect()->route('admin.students')->with('success', 'Student removed.');
    }
}
