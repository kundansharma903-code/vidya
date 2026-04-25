<?php

namespace App\Http\Controllers\SubAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    private const SUBJECT_NAMES = ['P' => 'Physics', 'C' => 'Chemistry', 'B' => 'Botany', 'Z' => 'Zoology'];

    // Upload History
    public function history(Request $request)
    {
        $instituteId = Auth::user()->institute_id;

        $uploads = DB::table('omr_upload_batches')
            ->join('tests',  'tests.id',  '=', 'omr_upload_batches.test_id')
            ->join('users',  'users.id',  '=', 'omr_upload_batches.uploaded_by')
            ->where('omr_upload_batches.institute_id', $instituteId)
            ->orderByDesc('omr_upload_batches.created_at')
            ->get([
                'omr_upload_batches.id',
                'omr_upload_batches.file_name',
                'omr_upload_batches.file_size',
                'omr_upload_batches.status',
                'omr_upload_batches.total_rows',
                'omr_upload_batches.matched_rows',
                'omr_upload_batches.unmatched_rows',
                'omr_upload_batches.created_at',
                'omr_upload_batches.completed_at',
                'tests.id as test_id',
                'tests.name as test_name',
                'tests.test_code',
                'tests.test_date',
                'tests.status as test_status',
                'users.name as uploaded_by',
            ]);

        return view('sub-admin.results.history', compact('uploads'));
    }

    // All students ranked for a test
    public function testResults(Request $request, $testId)
    {
        $instituteId = Auth::user()->institute_id;

        $test = DB::table('tests')->where('id', $testId)->where('institute_id', $instituteId)->first();
        if (!$test) abort(404);

        $search    = trim($request->query('search', ''));
        $batchFilter = $request->query('batch_id', '');

        $query = DB::table('test_results_cache')
            ->join('students', 'students.id', '=', 'test_results_cache.student_id')
            ->join('batches',  'batches.id',  '=', 'test_results_cache.batch_id')
            ->where('test_results_cache.test_id', $testId)
            ->orderBy('test_results_cache.rank_in_batch');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('students.name', 'like', "%{$search}%")
                  ->orWhere('students.roll_number', 'like', "%{$search}%");
            });
        }
        if ($batchFilter !== '') {
            $query->where('test_results_cache.batch_id', $batchFilter);
        }

        $results = $query->paginate(50, [
            'test_results_cache.id',
            'test_results_cache.student_id',
            'test_results_cache.total_marks',
            'test_results_cache.total_correct',
            'test_results_cache.total_incorrect',
            'test_results_cache.total_unattempted',
            'test_results_cache.rank_in_batch',
            'test_results_cache.percentile',
            'test_results_cache.subject_scores',
            'students.name as student_name',
            'students.roll_number',
            'batches.name as batch_name',
        ])->withQueryString();

        $batches = DB::table('test_batches')
            ->join('batches', 'batches.id', '=', 'test_batches.batch_id')
            ->where('test_batches.test_id', $testId)
            ->get(['batches.id', 'batches.name']);

        $totalStudents = DB::table('test_results_cache')->where('test_id', $testId)->count();
        $stats = DB::table('test_results_cache')->where('test_id', $testId)
            ->selectRaw('MAX(total_marks) as max, MIN(total_marks) as min, AVG(total_marks) as avg')
            ->first();

        return view('sub-admin.results.test-results', compact(
            'test', 'results', 'batches', 'search', 'batchFilter', 'totalStudents', 'stats'
        ));
    }

    // Individual student scorecard
    public function studentResult($testId, $studentId)
    {
        $instituteId = Auth::user()->institute_id;

        $test    = DB::table('tests')->where('id', $testId)->where('institute_id', $instituteId)->first();
        $student = DB::table('students')->where('id', $studentId)->where('institute_id', $instituteId)->first();
        if (!$test || !$student) abort(404);

        $batch = DB::table('batches')->where('id', $student->batch_id)->first(['name', 'code']);

        $result = DB::table('test_results_cache')
            ->where('test_id', $testId)->where('student_id', $studentId)->first();
        if (!$result) abort(404);

        $subjectScores = json_decode($result->subject_scores ?? '{}', true);

        // Enrich subject scores with names
        $subjectBreakdown = [];
        foreach ($subjectScores as $code => $data) {
            $subjectBreakdown[] = array_merge($data, [
                'code' => $code,
                'name' => self::SUBJECT_NAMES[$code] ?? $code,
            ]);
        }

        // Topic mastery for this student
        $mastery = DB::table('student_subtopic_mastery')
            ->join('curriculum_nodes', 'curriculum_nodes.id', '=', 'student_subtopic_mastery.curriculum_node_id')
            ->join('subjects', 'subjects.id', '=', 'student_subtopic_mastery.subject_id')
            ->where('student_subtopic_mastery.student_id', $studentId)
            ->where('student_subtopic_mastery.institute_id', $instituteId)
            ->orderBy('subjects.code')
            ->orderBy('curriculum_nodes.code')
            ->get([
                'curriculum_nodes.name as topic_name',
                'curriculum_nodes.code as topic_code',
                'subjects.name as subject_name',
                'subjects.code as subject_code',
                'student_subtopic_mastery.mastery_percentage',
                'student_subtopic_mastery.accuracy_percentage',
                'student_subtopic_mastery.total_questions_attempted',
                'student_subtopic_mastery.total_questions_correct',
                'student_subtopic_mastery.total_marks_earned',
                'student_subtopic_mastery.total_marks_possible',
            ]);

        // Group mastery by subject
        $masteryBySubject = [];
        foreach ($mastery as $m) {
            $masteryBySubject[$m->subject_name][] = $m;
        }

        // Rank context: who is above/below
        $totalInTest = DB::table('test_results_cache')->where('test_id', $testId)->count();

        // Question-level responses for answer review (first 45 only for now)
        $responses = DB::table('student_responses')
            ->join('test_questions', 'test_questions.id', '=', 'student_responses.test_question_id')
            ->where('student_responses.test_id', $testId)
            ->where('student_responses.student_id', $studentId)
            ->orderBy('test_questions.question_number')
            ->get([
                'test_questions.question_number',
                'test_questions.topic_code',
                'test_questions.correct_answer',
                'student_responses.submitted_answer',
                'student_responses.is_correct',
                'student_responses.marks_awarded',
            ]);

        return view('sub-admin.results.student-result', compact(
            'test', 'student', 'batch', 'result',
            'subjectBreakdown', 'masteryBySubject', 'responses', 'totalInTest'
        ));
    }
}
