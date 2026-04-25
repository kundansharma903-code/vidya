<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    private function instituteId(): int
    {
        return Auth::user()->institute_id;
    }

    public function index(Request $request)
    {
        $instituteId = $this->instituteId();

        $query = DB::table('courses')
            ->where('courses.institute_id', $instituteId)
            ->leftJoin('batches', 'batches.course_id', '=', 'courses.id')
            ->select(
                'courses.*',
                DB::raw('COUNT(DISTINCT batches.id) as batch_count')
            )
            ->groupBy('courses.id');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('courses.name', 'like', $s)
                  ->orWhere('courses.code', 'like', $s);
            });
        }

        if ($request->filled('exam_type')) {
            $query->where('courses.exam_type', $request->exam_type);
        }

        if ($request->filled('status')) {
            $query->where('courses.is_active', $request->status === 'active' ? 1 : 0);
        }

        $courses = $query->orderByDesc('courses.created_at')->paginate(20)->withQueryString();

        $stats = DB::table('courses')->where('institute_id', $instituteId)->selectRaw('
            COUNT(*) as total,
            SUM(is_active) as active_count,
            SUM(CASE WHEN exam_type = "NEET" THEN 1 ELSE 0 END) as neet_count,
            SUM(CASE WHEN exam_type IN ("JEE_MAIN","JEE_ADVANCED") THEN 1 ELSE 0 END) as jee_count
        ')->first();

        return view('admin.courses.index', compact('courses', 'stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'code'            => 'required|string|max:20',
            'exam_type'       => 'required|in:NEET,JEE_MAIN,JEE_ADVANCED,OTHER',
            'target_year'     => 'required|digits:4|integer|min:2024|max:2035',
            'duration_months' => 'required|integer|min:1|max:60',
            'total_questions' => 'nullable|integer|min:1',
        ]);

        $data['institute_id'] = $this->instituteId();
        $data['is_active']    = true;

        DB::table('courses')->insert($data + ['created_at' => now(), 'updated_at' => now()]);

        return redirect()->route('admin.courses')->with('success', 'Course created successfully.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100',
            'code'            => 'required|string|max:20',
            'exam_type'       => 'required|in:NEET,JEE_MAIN,JEE_ADVANCED,OTHER',
            'target_year'     => 'required|digits:4|integer|min:2024|max:2035',
            'duration_months' => 'required|integer|min:1|max:60',
            'total_questions' => 'nullable|integer|min:1',
        ]);

        DB::table('courses')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->update($data + ['updated_at' => now()]);

        return redirect()->route('admin.courses')->with('success', 'Course updated.');
    }

    public function toggle(int $id)
    {
        $course = DB::table('courses')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->first();

        if ($course) {
            DB::table('courses')->where('id', $id)->update([
                'is_active'  => !$course->is_active,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.courses')->with('success', 'Course status updated.');
    }

    public function destroy(int $id)
    {
        DB::table('courses')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->delete();

        return redirect()->route('admin.courses')->with('success', 'Course deleted.');
    }
}
