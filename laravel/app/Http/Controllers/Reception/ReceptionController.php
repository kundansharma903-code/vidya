<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceptionController extends Controller
{
    private function base(): array
    {
        $user        = Auth::user();
        $instituteId = $user->institute_id;
        return [$user, $instituteId];
    }

    // ── Dashboard ─────────────────────────────────────────────────────────────
    public function dashboard()
    {
        [$user, $instituteId] = $this->base();

        $totalStudents  = DB::table('students')->where('institute_id', $instituteId)->where('is_active', 1)->count();
        $totalTests     = DB::table('tests')->where('institute_id', $instituteId)->where('status', 'analyzed')->count();

        $todayStart = now()->startOfDay();
        $weekStart  = now()->startOfWeek();

        $walkInsToday = DB::table('walk_in_logs')
            ->where('reception_user_id', $user->id)
            ->where('viewed_at', '>=', $todayStart)
            ->count();

        $walkInsWeek = DB::table('walk_in_logs')
            ->where('reception_user_id', $user->id)
            ->where('viewed_at', '>=', $weekStart)
            ->count();

        // Recent walk-ins today (last 8)
        $recentWalkIns = DB::table('walk_in_logs as w')
            ->join('students as s', 's.id', '=', 'w.student_id')
            ->leftJoin('tests as t', 't.id', '=', 'w.test_id')
            ->where('w.reception_user_id', $user->id)
            ->where('w.viewed_at', '>=', $todayStart)
            ->orderByDesc('w.viewed_at')
            ->limit(8)
            ->get([
                'w.id', 'w.viewed_at', 'w.query_type',
                's.name as student_name', 's.roll_number',
                't.name as test_name', 't.test_code',
            ]);

        // Latest analyzed tests
        $latestTests = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->orderByDesc('test_date')
            ->limit(6)
            ->get();

        // Batches for quick-filter chips
        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('reception.dashboard', compact(
            'user', 'totalStudents', 'totalTests',
            'walkInsToday', 'walkInsWeek',
            'recentWalkIns', 'latestTests', 'batches'
        ));
    }

    // ── Student Search ────────────────────────────────────────────────────────
    public function students(Request $request)
    {
        [$user, $instituteId] = $this->base();

        $search      = trim($request->get('search', ''));
        $batchFilter = $request->get('batch_id', '');
        $sortBy      = $request->get('sort', 'name');

        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        $query = DB::table('students as s')
            ->join('batches as b', 'b.id', '=', 's.batch_id')
            ->where('s.institute_id', $instituteId)
            ->where('s.is_active', 1)
            ->select('s.id', 's.name', 's.roll_number', 's.batch_id', 'b.name as batch_name', 'b.code as batch_code');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('s.name', 'like', "%{$search}%")
                  ->orWhere('s.roll_number', 'like', "%{$search}%");
            });
        }
        if ($batchFilter !== '') {
            $query->where('s.batch_id', $batchFilter);
        }

        $query->orderBy($sortBy === 'roll' ? 's.roll_number' : 's.name');

        $students = $query->paginate(30)->withQueryString();

        // Attach latest test score per student
        $studentIds = $students->pluck('id');
        $latestScores = DB::table('test_results_cache as r')
            ->join('tests as t', 't.id', '=', 'r.test_id')
            ->whereIn('r.student_id', $studentIds)
            ->select('r.student_id', 'r.total_marks', 'r.rank_in_batch', 'r.test_id', 't.name as test_name', 't.test_code', 't.total_questions')
            ->orderByDesc('t.test_date')
            ->get()
            ->groupBy('student_id')
            ->map(fn($rows) => $rows->first());

        return view('reception.students', compact(
            'user', 'students', 'batches', 'latestScores',
            'search', 'batchFilter', 'sortBy'
        ));
    }

    // ── All Tests ─────────────────────────────────────────────────────────────
    public function tests(Request $request)
    {
        [$user, $instituteId] = $this->base();

        $search    = trim($request->get('search', ''));
        $dateFrom  = $request->get('date_from', '');
        $dateTo    = $request->get('date_to', '');

        $query = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->orderByDesc('test_date');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('test_code', 'like', "%{$search}%");
            });
        }
        if ($dateFrom !== '') $query->where('test_date', '>=', $dateFrom);
        if ($dateTo   !== '') $query->where('test_date', '<=', $dateTo);

        $tests = $query->paginate(20)->withQueryString();

        // Attach stats per test
        $testIds = $tests->pluck('id');
        $statsMap = DB::table('test_results_cache')
            ->whereIn('test_id', $testIds)
            ->groupBy('test_id')
            ->selectRaw('test_id, COUNT(DISTINCT student_id) as students, AVG(total_marks) as avg_marks, MAX(total_marks) as max_marks')
            ->get()->keyBy('test_id');

        $batchCountMap = DB::table('test_batches')
            ->whereIn('test_id', $testIds)
            ->groupBy('test_id')
            ->selectRaw('test_id, COUNT(DISTINCT batch_id) as batch_count')
            ->get()->keyBy('test_id');

        return view('reception.tests', compact(
            'user', 'tests', 'statsMap', 'batchCountMap',
            'search', 'dateFrom', 'dateTo'
        ));
    }

    // ── Test Results — Rank-wise ──────────────────────────────────────────────
    public function testResults(Request $request, $testId)
    {
        [$user, $instituteId] = $this->base();

        $test = DB::table('tests')
            ->where('id', $testId)
            ->where('institute_id', $instituteId)
            ->first();
        abort_if(!$test, 404);

        $search      = trim($request->get('search', ''));
        $batchFilter = $request->get('batch_id', '');

        $query = DB::table('test_results_cache as r')
            ->join('students as s', 's.id', '=', 'r.student_id')
            ->join('batches as b',  'b.id', '=', 'r.batch_id')
            ->where('r.test_id', $testId)
            ->orderBy('r.rank_in_batch');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('s.name', 'like', "%{$search}%")
                  ->orWhere('s.roll_number', 'like', "%{$search}%");
            });
        }
        if ($batchFilter !== '') {
            $query->where('r.batch_id', $batchFilter);
        }

        $results = $query->paginate(50, [
            'r.student_id', 'r.total_marks', 'r.rank_in_batch', 'r.percentile',
            'r.total_correct', 'r.total_incorrect', 'r.total_unattempted',
            's.name as student_name', 's.roll_number',
            'b.name as batch_name',
        ])->withQueryString();

        $batches = DB::table('test_batches as tb')
            ->join('batches as b', 'b.id', '=', 'tb.batch_id')
            ->where('tb.test_id', $testId)
            ->get(['b.id', 'b.name']);

        $stats = DB::table('test_results_cache')
            ->where('test_id', $testId)
            ->selectRaw('COUNT(*) as total, MAX(total_marks) as highest, MIN(total_marks) as lowest, AVG(total_marks) as average')
            ->first();

        // Median
        $allMarks = DB::table('test_results_cache')->where('test_id', $testId)->orderBy('total_marks')->pluck('total_marks');
        $median = 0;
        if ($allMarks->count() > 0) {
            $mid = (int) floor($allMarks->count() / 2);
            $median = $allMarks->count() % 2 === 0
                ? ($allMarks[$mid - 1] + $allMarks[$mid]) / 2
                : $allMarks[$mid];
        }

        $maxMarks  = $test->total_questions * 4;
        $passCount = DB::table('test_results_cache')->where('test_id', $testId)->where('total_marks', '>=', $maxMarks * 0.35)->count();
        $passRate  = $stats->total > 0 ? (int) round($passCount / $stats->total * 100) : 0;

        return view('reception.test-results', compact(
            'user', 'test', 'results', 'batches', 'stats', 'median',
            'maxMarks', 'passRate', 'search', 'batchFilter'
        ));
    }

    // ── Student Result Detail ─────────────────────────────────────────────────
    public function studentResult($studentId, $testId)
    {
        [$user, $instituteId] = $this->base();

        $student = DB::table('students')->where('id', $studentId)->where('institute_id', $instituteId)->first();
        $test    = DB::table('tests')->where('id', $testId)->where('institute_id', $instituteId)->first();
        abort_if(!$student || !$test, 404);

        $batch  = DB::table('batches')->where('id', $student->batch_id)->first(['name', 'code']);
        $result = DB::table('test_results_cache')->where('test_id', $testId)->where('student_id', $studentId)->first();
        abort_if(!$result, 404);

        // Log the walk-in
        DB::table('walk_in_logs')->insert([
            'reception_user_id' => $user->id,
            'student_id'        => $studentId,
            'test_id'           => $testId,
            'viewed_at'         => now(),
            'query_type'        => 'result_lookup',
        ]);

        $subjectScores   = json_decode($result->subject_scores ?? '{}', true);
        $subjectNames    = ['P' => 'Physics', 'C' => 'Chemistry', 'B' => 'Botany', 'Z' => 'Zoology', 'M' => 'Mathematics'];
        $subjectBreakdown = [];
        foreach ($subjectScores as $code => $data) {
            $subjectBreakdown[] = array_merge($data, ['code' => $code, 'name' => $subjectNames[$code] ?? $code]);
        }

        // Q-by-Q responses
        $responses = DB::table('student_responses as sr')
            ->join('test_questions as tq', 'tq.id', '=', 'sr.test_question_id')
            ->where('sr.test_id', $testId)
            ->where('sr.student_id', $studentId)
            ->orderBy('tq.question_number')
            ->get([
                'tq.question_number', 'tq.topic_code', 'tq.correct_answer',
                'sr.submitted_answer', 'sr.is_correct', 'sr.marks_awarded',
            ]);

        $totalInTest = DB::table('test_results_cache')->where('test_id', $testId)->count();
        $maxMarks    = $test->total_questions * 4;

        return view('reception.student-result', compact(
            'user', 'student', 'test', 'batch', 'result',
            'subjectBreakdown', 'responses', 'totalInTest', 'maxMarks'
        ));
    }

    // ── Walk-in Logs ──────────────────────────────────────────────────────────
    public function walkIns(Request $request)
    {
        [$user, $instituteId] = $this->base();

        $range = $request->get('range', 'today');
        $from  = match($range) {
            'week'    => now()->startOfWeek(),
            'month'   => now()->startOfMonth(),
            default   => now()->startOfDay(),
        };

        $logs = DB::table('walk_in_logs as w')
            ->join('students as s', 's.id', '=', 'w.student_id')
            ->leftJoin('tests as t', 't.id', '=', 'w.test_id')
            ->join('users as u', 'u.id', '=', 'w.reception_user_id')
            ->where('w.reception_user_id', $user->id)
            ->where('w.viewed_at', '>=', $from)
            ->orderByDesc('w.viewed_at')
            ->paginate(40, [
                'w.id', 'w.viewed_at', 'w.query_type', 'w.student_id',
                's.name as student_name', 's.roll_number',
                't.name as test_name', 't.test_code', 't.id as test_id',
                'u.name as staff_name',
            ])->withQueryString();

        return view('reception.walk-ins', compact('user', 'logs', 'range'));
    }

    // ── Help ──────────────────────────────────────────────────────────────────
    public function help()
    {
        [$user] = $this->base();
        return view('reception.help', compact('user'));
    }
}
