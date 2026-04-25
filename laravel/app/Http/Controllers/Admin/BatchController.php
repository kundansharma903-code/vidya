<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    private function instituteId(): int
    {
        return Auth::user()->institute_id;
    }

    public function index(Request $request)
    {
        $instituteId = $this->instituteId();

        $query = DB::table('batches')
            ->where('batches.institute_id', $instituteId)
            ->join('courses', 'courses.id', '=', 'batches.course_id')
            ->leftJoin('students', 'students.batch_id', '=', 'batches.id')
            ->leftJoin('user_batch_assignments as uba', 'uba.batch_id', '=', 'batches.id')
            ->select(
                'batches.*',
                'courses.name as course_name',
                DB::raw('COUNT(DISTINCT students.id) as student_count'),
                DB::raw('COUNT(DISTINCT uba.id) as teacher_count')
            )
            ->groupBy('batches.id', 'courses.name');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('batches.name', 'like', $s)
                  ->orWhere('batches.code', 'like', $s);
            });
        }

        if ($request->filled('course_id')) {
            $query->where('batches.course_id', $request->course_id);
        }

        if ($request->filled('timing')) {
            $query->where('batches.timing_label', 'like', '%' . $request->timing . '%');
        }

        if ($request->filled('status')) {
            $query->where('batches.is_active', $request->status === 'active' ? 1 : 0);
        }

        $batches = $query->orderByDesc('batches.created_at')->paginate(8)->withQueryString();

        $stats = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->selectRaw('COUNT(*) as total, SUM(is_active) as active_count')
            ->first();

        $courseCount = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->distinct('course_id')
            ->count('course_id');

        $avgStudents = DB::table('students')
            ->join('batches', 'batches.id', '=', 'students.batch_id')
            ->where('batches.institute_id', $instituteId)
            ->selectRaw('ROUND(COUNT(students.id) / NULLIF(COUNT(DISTINCT batches.id), 0)) as avg_students')
            ->value('avg_students');

        $courses = DB::table('courses')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.batches.index', compact('batches', 'stats', 'courseCount', 'avgStudents', 'courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'nullable|string|max:20',
            'course_id'     => 'required|integer|exists:courses,id',
            'timing_label'  => 'nullable|string|max:50',
            'capacity'      => 'required|integer|min:1|max:1000',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
        ]);

        $data['institute_id'] = $this->instituteId();
        $data['is_active']    = true;

        DB::table('batches')->insert($data + ['created_at' => now(), 'updated_at' => now()]);

        return redirect()->route('admin.batches')->with('success', 'Batch created successfully.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'nullable|string|max:20',
            'course_id'     => 'required|integer|exists:courses,id',
            'timing_label'  => 'nullable|string|max:50',
            'capacity'      => 'required|integer|min:1|max:1000',
            'start_date'    => 'nullable|date',
            'end_date'      => 'nullable|date|after_or_equal:start_date',
        ]);

        DB::table('batches')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->update($data + ['updated_at' => now()]);

        return redirect()->route('admin.batches')->with('success', 'Batch updated.');
    }

    public function toggle(int $id)
    {
        $batch = DB::table('batches')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->first();

        if ($batch) {
            DB::table('batches')->where('id', $id)->update([
                'is_active'  => !$batch->is_active,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.batches')->with('success', 'Batch status updated.');
    }

    public function destroy(int $id)
    {
        DB::table('batches')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->delete();

        return redirect()->route('admin.batches')->with('success', 'Batch deleted.');
    }
}
