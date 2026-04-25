<?php

namespace App\Http\Controllers\SubAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $instituteId = Auth::user()->institute_id;
        $tab         = $request->query('tab', 'all');

        $base = DB::table('tests')->where('institute_id', $instituteId);

        // Tab counts
        $counts = [
            'all'            => (clone $base)->count(),
            'pending_upload' => (clone $base)->whereIn('status', ['conducted', 'responses_uploaded'])->count(),
            'analyzed'       => (clone $base)->where('status', 'analyzed')->count(),
            'scheduled'      => (clone $base)->where('status', 'scheduled')->count(),
        ];

        // Filtered query
        $query = (clone $base)->orderBy('test_date', 'desc');
        match ($tab) {
            'pending_upload' => $query->whereIn('status', ['conducted', 'responses_uploaded']),
            'analyzed'       => $query->where('status', 'analyzed'),
            'scheduled'      => $query->where('status', 'scheduled'),
            default          => null,
        };

        $tests = $query->get();

        // Attach batch count + student count
        $tests = $tests->map(function ($test) {
            $batchIds = DB::table('test_batches')->where('test_id', $test->id)->pluck('batch_id');
            $test->batch_count   = $batchIds->count();
            $test->student_count = $batchIds->isNotEmpty()
                ? DB::table('user_batch_assignments')->whereIn('batch_id', $batchIds)->distinct('user_id')->count('user_id')
                : 0;
            return $test;
        });

        return view('sub-admin.tests.index', compact('tests', 'counts', 'tab'));
    }

    public function create()
    {
        $instituteId = Auth::user()->institute_id;

        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $teachers = DB::table('users')
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $subjects = DB::table('subjects')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get(['id', 'name', 'code']);

        $nextCode = $this->generateTestCode($instituteId);

        return view('sub-admin.tests.create', compact('batches', 'teachers', 'subjects', 'nextCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'test_code'         => 'required|string|max:50|unique:tests,test_code',
            'name'              => 'required|string|max:255',
            'test_date'         => 'required|date',
            'pattern'           => 'required|string|max:50',
            'test_type'         => 'required|in:dpt,weekly,mock,flt,chapter,revision',
            'batch_ids'         => 'required|array|min:1',
            'batch_ids.*'       => 'exists:batches,id',
            'teacher_ids'       => 'required|array|min:1',
            'teacher_ids.*'     => 'exists:users,id',
            'correct_marks'     => 'required|numeric|min:0',
            'incorrect_marks'   => 'required|numeric|max:0',
            'unattempted_marks' => 'required|numeric',
            'invalid_marks'     => 'required|numeric|max:0',
        ]);

        $instituteId = Auth::user()->institute_id;

        // Derive total_questions and total_marks from pattern
        [$totalQ, $totalM] = $this->patternStats($request->pattern, $request->correct_marks);

        $testId = DB::table('tests')->insertGetId([
            'institute_id'       => $instituteId,
            'course_id'          => null,
            'test_code'          => $request->test_code,
            'name'               => $request->name,
            'test_type'          => $request->test_type,
            'test_date'          => $request->test_date,
            'pattern'            => $request->pattern,
            'duration_minutes'   => $this->patternDuration($request->pattern),
            'total_questions'    => $totalQ,
            'total_marks'        => $totalM,
            'correct_marks'      => $request->correct_marks,
            'incorrect_marks'    => $request->incorrect_marks,
            'unattempted_marks'  => $request->unattempted_marks,
            'invalid_marks'      => $request->invalid_marks,
            'status'             => 'scheduled',
            'created_by'         => Auth::id(),
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Attach batches
        $batchRows = array_map(fn($id) => [
            'test_id'    => $testId,
            'batch_id'   => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ], $request->batch_ids);
        DB::table('test_batches')->insert($batchRows);

        // Attach teachers
        $teacherRows = array_map(fn($id) => [
            'test_id'    => $testId,
            'teacher_id' => $id,
            'created_at' => now(),
            'updated_at' => now(),
        ], $request->teacher_ids);
        DB::table('test_teacher_assignments')->insert($teacherRows);

        return redirect()->route('sub-admin.tests.index')
            ->with('success', 'Test "' . $request->name . '" created successfully.');
    }

    private function generateTestCode(int $instituteId): string
    {
        $count = DB::table('tests')->where('institute_id', $instituteId)->count();
        return 'NEET-MOCK-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }

    private function patternStats(string $pattern, float $correctMarks): array
    {
        return match (true) {
            str_starts_with($pattern, 'NEET')   => [180, (int)(180 * $correctMarks)],
            str_starts_with($pattern, 'JEE')    => [90,  (int)(90  * $correctMarks)],
            str_starts_with($pattern, 'AIIMS')  => [200, (int)(200 * $correctMarks)],
            default                             => [0, 0],
        };
    }

    private function patternDuration(string $pattern): int
    {
        return match (true) {
            str_starts_with($pattern, 'NEET')  => 200,
            str_starts_with($pattern, 'JEE')   => 180,
            str_starts_with($pattern, 'AIIMS') => 210,
            default                            => 180,
        };
    }
}
