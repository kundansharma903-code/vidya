<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    private function scope(): array
    {
        $user        = Auth::user();
        $instituteId = $user->institute_id;
        $teacherId   = $user->id;
        $subjectId   = $user->primary_subject_id;

        $batchIds = DB::table('user_batch_assignments')
            ->where('user_id', $teacherId)
            ->pluck('batch_id');

        $nodeIds = $subjectId
            ? DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subjectId)
                ->pluck('id')
            : collect();

        $studentIds = DB::table('students')
            ->where('institute_id', $instituteId)
            ->whereIn('batch_id', $batchIds)
            ->where('is_active', 1)
            ->pluck('id');

        $subject = $subjectId
            ? DB::table('subjects')->where('id', $subjectId)->first()
            : null;

        return [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject];
    }

    // ── Step 4: My Students ──────────────────────────────────────────────────
    public function students(Request $request)
    {
        [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject] = $this->scope();

        $search      = $request->get('search', '');
        $batchFilter = $request->get('batch_id', '');

        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->whereIn('id', $batchIds)
            ->get();

        $query = DB::table('students as s')
            ->join('batches as b', 'b.id', '=', 's.batch_id')
            ->where('s.institute_id', $instituteId)
            ->whereIn('s.batch_id', $batchIds)
            ->where('s.is_active', 1);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('s.name', 'like', "%$search%")
                  ->orWhere('s.roll_number', 'like', "%$search%");
            });
        }
        if ($batchFilter) {
            $query->where('s.batch_id', $batchFilter);
        }

        $studentRows = $query
            ->select('s.id', 's.name', 's.roll_number', 's.medium', 'b.name as batch_name', 'b.id as batch_id')
            ->orderBy('s.name')
            ->get();

        // Attach mastery avg per student
        $masteryMap = collect();
        if ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty()) {
            $masteryMap = DB::table('student_subtopic_mastery')
                ->whereIn('student_id', $studentIds)
                ->whereIn('curriculum_node_id', $nodeIds)
                ->groupBy('student_id')
                ->selectRaw('student_id, AVG(mastery_percentage) as avg_m, COUNT(DISTINCT curriculum_node_id) as topics_count')
                ->get()
                ->keyBy('student_id');
        }

        // Attach last test rank
        $lastTest = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->orderByDesc('test_date')
            ->first();

        $rankMap = collect();
        if ($lastTest) {
            $rankMap = DB::table('test_results_cache')
                ->where('test_id', $lastTest->id)
                ->whereIn('student_id', $studentIds)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.students', compact(
            'user', 'subject', 'batches', 'studentRows',
            'masteryMap', 'rankMap', 'lastTest',
            'search', 'batchFilter'
        ));
    }

    // ── Step 8: Student Deep-dive ────────────────────────────────────────────
    public function studentDetail($studentId)
    {
        [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject] = $this->scope();

        $student = DB::table('students as s')
            ->join('batches as b', 'b.id', '=', 's.batch_id')
            ->where('s.id', $studentId)
            ->where('s.institute_id', $instituteId)
            ->select('s.*', 'b.name as batch_name')
            ->first();

        abort_if(!$student, 404);

        // Topic mastery for this subject
        $topicMastery = collect();
        if ($nodeIds->isNotEmpty()) {
            $topicMastery = DB::table('student_subtopic_mastery as m')
                ->join('curriculum_nodes as cn', 'cn.id', '=', 'm.curriculum_node_id')
                ->where('m.student_id', $studentId)
                ->whereIn('m.curriculum_node_id', $nodeIds)
                ->select('cn.name as topic_name', 'cn.code as topic_code',
                         'm.mastery_percentage', 'm.total_questions_attempted', 'm.total_questions_correct')
                ->orderByDesc('m.mastery_percentage')
                ->get();
        }

        // Test history
        $testHistory = DB::table('test_results_cache as rc')
            ->join('tests as t', 't.id', '=', 'rc.test_id')
            ->where('rc.student_id', $studentId)
            ->where('t.institute_id', $instituteId)
            ->select('t.name', 't.test_code', 't.test_date', 't.total_questions',
                     'rc.total_marks', 'rc.total_correct', 'rc.total_incorrect',
                     'rc.rank_in_batch', 'rc.percentile')
            ->orderByDesc('t.test_date')
            ->get();

        $avgMastery = $topicMastery->isNotEmpty()
            ? (int) round($topicMastery->avg('mastery_percentage'))
            : 0;

        return view('teacher.student-detail', compact(
            'user', 'subject', 'student', 'topicMastery', 'testHistory', 'avgMastery'
        ));
    }

    // ── Step 5: Class Heatmap ────────────────────────────────────────────────
    public function heatmap()
    {
        [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject] = $this->scope();

        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->whereIn('id', $batchIds)
            ->orderBy('name')
            ->get();

        $topics = collect();
        $heatmap = [];

        if ($nodeIds->isNotEmpty()) {
            $topics = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subjectId)
                ->whereIn('id', $nodeIds)
                ->orderBy('code')
                ->get();

            // For each topic × batch, compute avg mastery
            foreach ($topics as $topic) {
                $heatmap[$topic->id] = [];
                foreach ($batches as $batch) {
                    $batchStudentIds = DB::table('students')
                        ->where('institute_id', $instituteId)
                        ->where('batch_id', $batch->id)
                        ->where('is_active', 1)
                        ->pluck('id');

                    $avg = $batchStudentIds->isNotEmpty()
                        ? DB::table('student_subtopic_mastery')
                            ->where('curriculum_node_id', $topic->id)
                            ->whereIn('student_id', $batchStudentIds)
                            ->avg('mastery_percentage')
                        : null;

                    $heatmap[$topic->id][$batch->id] = $avg !== null ? (int) round($avg) : null;
                }
            }
        }

        return view('teacher.heatmap', compact(
            'user', 'subject', 'batches', 'topics', 'heatmap'
        ));
    }

    // ── Step 6: Class Insights ───────────────────────────────────────────────
    public function insights()
    {
        [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject] = $this->scope();

        // Mastery distribution buckets
        $dist = ['strong' => 0, 'average' => 0, 'weak' => 0];
        $studentAvgList = collect();

        if ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty()) {
            $studentAvgList = DB::table('student_subtopic_mastery')
                ->whereIn('student_id', $studentIds)
                ->whereIn('curriculum_node_id', $nodeIds)
                ->groupBy('student_id')
                ->selectRaw('student_id, AVG(mastery_percentage) as avg_m')
                ->get();

            foreach ($studentAvgList as $row) {
                if ($row->avg_m >= 70) $dist['strong']++;
                elseif ($row->avg_m >= 40) $dist['average']++;
                else $dist['weak']++;
            }
        }

        $totalStudents = $studentAvgList->count() ?: 1;

        // Load all students with batch name once
        $allStudents = DB::table('students as s')
            ->join('batches as b', 'b.id', '=', 's.batch_id')
            ->whereIn('s.id', $studentIds)
            ->where('s.is_active', 1)
            ->select('s.id', 's.name', 'b.name as batch_name')
            ->get()
            ->map(function ($s) use ($studentAvgList) {
                $s->avg_m = (int) round($studentAvgList->firstWhere('student_id', $s->id)?->avg_m ?? 0);
                return $s;
            });

        // Top 5 students
        $toppers = $allStudents->sortByDesc('avg_m')->take(5)->values();

        // At-risk (bottom 5 by avg mastery)
        $atRisk = $allStudents->filter(fn($s) => $s->avg_m < 40)->sortBy('avg_m')->take(5)->values();

        // Recent test scores
        $recentTests = DB::table('tests as t')
            ->where('t.institute_id', $instituteId)
            ->where('t.status', 'analyzed')
            ->orderByDesc('t.test_date')
            ->limit(6)
            ->get();

        foreach ($recentTests as $t) {
            $t->avg_marks = (int) round(
                DB::table('test_results_cache')
                    ->where('test_id', $t->id)
                    ->whereIn('student_id', $studentIds)
                    ->avg('total_marks') ?? 0
            );
            $t->max_marks = $t->total_questions * 4;
        }

        return view('teacher.insights', compact(
            'user', 'subject', 'dist', 'totalStudents',
            'toppers', 'atRisk', 'recentTests'
        ));
    }

    // ── Step 9: Weak Topics ──────────────────────────────────────────────────
    public function weakTopics()
    {
        [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject] = $this->scope();

        $topics = collect();
        if ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty()) {
            $topics = DB::table('student_subtopic_mastery as m')
                ->join('curriculum_nodes as cn', 'cn.id', '=', 'm.curriculum_node_id')
                ->whereIn('m.student_id', $studentIds)
                ->whereIn('m.curriculum_node_id', $nodeIds)
                ->groupBy('m.curriculum_node_id', 'cn.name', 'cn.code')
                ->selectRaw('cn.name as topic_name, cn.code as topic_code,
                             AVG(m.mastery_percentage) as avg_mastery,
                             SUM(m.total_questions_correct) as total_correct,
                             SUM(m.total_questions_attempted) as total_attempted,
                             COUNT(DISTINCT m.student_id) as student_count')
                ->orderByRaw('AVG(m.mastery_percentage) ASC')
                ->get();
        }

        return view('teacher.weak-topics', compact('user', 'subject', 'topics'));
    }

    // ── Step 7: Topic Deep-dive ──────────────────────────────────────────────
    public function topicDetail($topicCode)
    {
        [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject] = $this->scope();

        $node = DB::table('curriculum_nodes')
            ->where('institute_id', $instituteId)
            ->where('code', $topicCode)
            ->first();

        abort_if(!$node, 404);

        $students = DB::table('student_subtopic_mastery as m')
            ->join('students as s', 's.id', '=', 'm.student_id')
            ->join('batches as b', 'b.id', '=', 's.batch_id')
            ->where('m.curriculum_node_id', $node->id)
            ->whereIn('m.student_id', $studentIds)
            ->select('s.name', 's.roll_number', 'b.name as batch_name',
                     'm.mastery_percentage', 'm.total_questions_correct', 'm.total_questions_attempted')
            ->orderByDesc('m.mastery_percentage')
            ->get();

        $avgMastery = $students->isNotEmpty() ? (int) round($students->avg('mastery_percentage')) : 0;

        return view('teacher.topic-detail', compact(
            'user', 'subject', 'node', 'students', 'avgMastery'
        ));
    }

    // ── Step 10: My Tests ────────────────────────────────────────────────────
    public function tests()
    {
        [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject] = $this->scope();

        $tests = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->orderByDesc('test_date')
            ->get();

        foreach ($tests as $t) {
            $stats = DB::table('test_results_cache')
                ->where('test_id', $t->id)
                ->whereIn('student_id', $studentIds)
                ->selectRaw('COUNT(*) as cnt, AVG(total_marks) as avg_m, MAX(total_marks) as max_m, MIN(total_marks) as min_m')
                ->first();

            $t->student_count = $stats->cnt ?? 0;
            $t->avg_marks     = (int) round($stats->avg_m ?? 0);
            $t->max_marks_got = $stats->max_m ?? 0;
            $t->min_marks     = $stats->min_m ?? 0;
            $t->max_possible  = $t->total_questions * 4;
        }

        return view('teacher.tests', compact('user', 'subject', 'tests'));
    }

    // ── Step 11: Notifications ───────────────────────────────────────────────
    public function notifications()
    {
        [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject] = $this->scope();

        $notifications = DB::table('notifications')
            ->where('institute_id', $instituteId)
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        return view('teacher.notifications', compact('user', 'notifications'));
    }

    // ── Step 12: Help ────────────────────────────────────────────────────────
    public function help()
    {
        [$user] = $this->scope();
        return view('teacher.help', compact('user'));
    }

    // ── Rankings ─────────────────────────────────────────────────────────────
    public function rankings(Request $request)
    {
        [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject] = $this->scope();

        $testId      = $request->get('test_id', '');
        $batchFilter = $request->get('batch_id', '');
        $search      = trim($request->get('search', ''));

        $tests = DB::table('tests as t')
            ->join('test_batches as tb', 'tb.test_id', '=', 't.id')
            ->where('t.institute_id', $instituteId)
            ->where('t.status', 'analyzed')
            ->whereIn('tb.batch_id', $batchIds)
            ->select('t.id', 't.name', 't.test_code', 't.test_date')
            ->orderByDesc('t.test_date')
            ->distinct()
            ->get();

        if (!$testId && $tests->isNotEmpty()) {
            $testId = $tests->first()->id;
        }

        $test = $testId ? DB::table('tests')->where('id', $testId)->where('institute_id', $instituteId)->first() : null;

        $results = collect(); $stats = null; $batches = collect();
        $maxMarks = 0; $median = 0; $passRate = 0; $totalStudents = 0;

        if ($test) {
            $maxMarks = $test->total_questions * 4;
            $batches  = DB::table('batches')->where('institute_id', $instituteId)->whereIn('id', $batchIds)->get(['id', 'name']);

            $query = DB::table('test_results_cache as r')
                ->join('students as s', 's.id', '=', 'r.student_id')
                ->join('batches as b',  'b.id', '=', 'r.batch_id')
                ->where('r.test_id', $testId)
                ->whereIn('r.student_id', $studentIds)
                ->orderBy('r.rank_in_batch');

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('s.name', 'like', "%{$search}%")->orWhere('s.roll_number', 'like', "%{$search}%");
                });
            }
            if ($batchFilter !== '') $query->where('r.batch_id', $batchFilter);

            $results = $query->paginate(50, [
                'r.student_id','r.total_marks','r.rank_in_batch','r.percentile',
                'r.total_correct','r.total_incorrect','r.total_unattempted',
                's.name as student_name','s.roll_number','b.name as batch_name',
            ])->withQueryString();

            $allMarks    = DB::table('test_results_cache')->where('test_id', $testId)->whereIn('student_id', $studentIds)->orderBy('total_marks')->pluck('total_marks');
            $totalStudents = $allMarks->count();
            if ($totalStudents > 0) {
                $stats  = (object)['highest' => $allMarks->max(), 'lowest' => $allMarks->min(), 'average' => $allMarks->avg()];
                $median = $allMarks->values()->get((int)floor($totalStudents / 2), 0);
                $passed = $allMarks->filter(fn($m) => $maxMarks > 0 && $m >= $maxMarks * 0.25)->count();
                $passRate = (int)round($passed / $totalStudents * 100);
            }
        }

        return view('teacher.rankings', compact(
            'user','subject','tests','test','testId',
            'results','stats','batches','maxMarks','median','passRate',
            'totalStudents','search','batchFilter'
        ));
    }

    // ── Student Deep-Dive ─────────────────────────────────────────────────────
    public function studentDeepDive($studentId)
    {
        [$user, $instituteId, $batchIds, $subjectId, $nodeIds, $studentIds, $subject] = $this->scope();

        $student = DB::table('students as s')
            ->join('batches as b', 'b.id', '=', 's.batch_id')
            ->where('s.id', $studentId)
            ->where('s.institute_id', $instituteId)
            ->whereIn('s.batch_id', $batchIds)
            ->select('s.*', 'b.name as batch_name')
            ->first();
        abort_if(!$student, 404);

        $topicMastery = collect();
        if ($nodeIds->isNotEmpty()) {
            $topicMastery = DB::table('student_subtopic_mastery as m')
                ->join('curriculum_nodes as cn', 'cn.id', '=', 'm.curriculum_node_id')
                ->where('m.student_id', $studentId)
                ->whereIn('m.curriculum_node_id', $nodeIds)
                ->select('cn.name as topic_name','cn.code as topic_code',
                         'm.mastery_percentage','m.total_questions_attempted','m.total_questions_correct')
                ->orderBy('cn.code')
                ->get();
        }

        $masteryBySubject = $subject && $topicMastery->isNotEmpty()
            ? [$subject->name => $topicMastery->all()]
            : [];

        $testHistory = DB::table('test_results_cache as rc')
            ->join('tests as t', 't.id', '=', 'rc.test_id')
            ->where('rc.student_id', $studentId)
            ->where('t.institute_id', $instituteId)
            ->select('t.id','t.name','t.test_code','t.test_date','t.total_questions',
                     'rc.total_marks','rc.total_correct','rc.total_incorrect',
                     'rc.rank_in_batch','rc.percentile')
            ->orderByDesc('t.test_date')
            ->get();

        $avgMastery  = $topicMastery->isNotEmpty() ? (int)round($topicMastery->avg('mastery_percentage')) : 0;
        $testsCount  = $testHistory->count();
        $avgScore    = $testsCount > 0 ? (int)round($testHistory->avg('total_marks')) : 0;
        $bestRank    = $testsCount > 0 ? $testHistory->min('rank_in_batch') : null;
        $weakTopics  = $topicMastery->sortBy('mastery_percentage')->take(8)->values();

        return view('teacher.student-deep-dive', compact(
            'user','subject','student','topicMastery','masteryBySubject',
            'testHistory','avgMastery','testsCount','avgScore','bestRank','weakTopics'
        ));
    }
}
