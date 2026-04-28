<?php

namespace App\Http\Controllers\AcademicHead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademicHeadController extends Controller
{
    private function base(): array
    {
        $user        = Auth::user();
        $instituteId = $user->institute_id;
        $subjects    = DB::table('subjects')->where('institute_id', $instituteId)->where('is_active', 1)->orderBy('name')->get();
        return [$user, $instituteId, $subjects];
    }

    // ── Curriculum Coverage ──────────────────────────────────────────────────
    public function curriculumCoverage()
    {
        [$user, $instituteId, $subjects] = $this->base();

        $subjectData = [];
        foreach ($subjects as $subj) {
            $nodes = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subj->id)
                ->get();

            $coveredIds = DB::table('student_subtopic_mastery')
                ->whereIn('curriculum_node_id', $nodes->pluck('id'))
                ->distinct('curriculum_node_id')
                ->pluck('curriculum_node_id');

            $uncoveredNodes = $nodes->whereNotIn('id', $coveredIds->toArray())->values();

            $total   = $nodes->count();
            $covered = $coveredIds->count();
            $pct     = $total > 0 ? (int) round($covered / $total * 100) : 0;

            $subjectData[] = [
                'id'             => $subj->id,
                'name'           => $subj->name,
                'code'           => $subj->code,
                'total'          => $total,
                'covered'        => $covered,
                'uncovered'      => $total - $covered,
                'pct'            => $pct,
                'uncoveredNodes' => $uncoveredNodes,
            ];
        }

        $totalNodes   = collect($subjectData)->sum('total');
        $coveredNodes = collect($subjectData)->sum('covered');
        $overallPct   = $totalNodes > 0 ? (int) round($coveredNodes / $totalNodes * 100) : 0;
        $pending      = $totalNodes - $coveredNodes;

        // Weeks to NEET: estimate (NEET is usually in May, count weeks from now)
        $neetDate     = now()->month <= 5 ? now()->setMonth(5)->setDay(4) : now()->addYear()->setMonth(5)->setDay(4);
        $weeksLeft    = (int) ceil(now()->diffInDays($neetDate) / 7);
        $subjectsBehind = collect($subjectData)->filter(fn($s) => $s['pct'] < 60)->count();

        return view('academic-head.curriculum-coverage', compact(
            'user', 'subjects', 'subjectData',
            'overallPct', 'pending', 'totalNodes', 'coveredNodes',
            'weeksLeft', 'subjectsBehind'
        ));
    }

    // ── Test Quality ─────────────────────────────────────────────────────────
    public function testQuality()
    {
        [$user, $instituteId, $subjects] = $this->base();

        $tests = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->orderByDesc('test_date')
            ->get();

        $totalNodes = DB::table('curriculum_nodes')->where('institute_id', $instituteId)->count();

        $testData = [];
        foreach ($tests as $t) {
            // Topic diversity: distinct topic_codes in this test's questions
            $distinctTopics = DB::table('test_questions')
                ->where('test_id', $t->id)
                ->whereNotNull('topic_code')
                ->distinct('topic_code')
                ->count('topic_code');

            $diversity = $totalNodes > 0 ? (int) round($distinctTopics / $totalNodes * 100) : 0;

            // Quality grade
            $grade = $diversity >= 80 ? 'A+' : ($diversity >= 60 ? 'A' : ($diversity >= 40 ? 'B' : 'C'));
            $gradeColor = match($grade) {
                'A+' => '#7fb685', 'A' => '#7a95c8', 'B' => '#d4a574', default => '#c87064'
            };

            $testData[] = [
                'id'          => $t->id,
                'name'        => $t->name,
                'code'        => $t->test_code,
                'date'        => $t->test_date,
                'questions'   => $t->total_questions,
                'diversity'   => $diversity,
                'grade'       => $grade,
                'gradeColor'  => $gradeColor,
            ];
        }

        $overallQuality  = $testData ? (int) round(collect($testData)->avg('diversity')) : 0;
        $avgDiversity    = $overallQuality;
        $belowStandard   = collect($testData)->filter(fn($t) => $t['diversity'] < 40)->count();

        return view('academic-head.test-quality', compact(
            'user', 'testData', 'overallQuality', 'avgDiversity', 'belowStandard'
        ));
    }

    // ── Subject Performance ──────────────────────────────────────────────────
    public function subjectPerformance()
    {
        [$user, $instituteId, $subjects] = $this->base();

        $subjectColors = ['P'=>'#7a95c8','C'=>'#d4a574','B'=>'#7fb685','Z'=>'#c87064','M'=>'#a8a39c'];

        $subjectData = [];
        foreach ($subjects as $subj) {
            $nodeIds = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subj->id)
                ->pluck('id');

            $stats = $nodeIds->isNotEmpty()
                ? DB::table('student_subtopic_mastery')
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->selectRaw('AVG(mastery_percentage) as avg_m, COUNT(DISTINCT student_id) as students,
                                 SUM(total_questions_correct) as correct, SUM(total_questions_attempted) as attempted')
                    ->first()
                : null;

            $avg  = $stats ? (int) round($stats->avg_m ?? 0) : 0;
            $covered = $nodeIds->isNotEmpty()
                ? DB::table('student_subtopic_mastery')->whereIn('curriculum_node_id', $nodeIds)->distinct('curriculum_node_id')->count('curriculum_node_id')
                : 0;

            // Weak topics for this subject
            $weakCount = $nodeIds->isNotEmpty()
                ? DB::table('student_subtopic_mastery')
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->groupBy('curriculum_node_id')
                    ->havingRaw('AVG(mastery_percentage) < 40')
                    ->count(DB::raw('DISTINCT curriculum_node_id'))
                : 0;

            $subjectData[] = [
                'id'        => $subj->id,
                'name'      => $subj->name,
                'code'      => $subj->code,
                'color'     => $subjectColors[$subj->code] ?? '#a8a39c',
                'avg'       => $avg,
                'total'     => $nodeIds->count(),
                'covered'   => $covered,
                'students'  => $stats->students ?? 0,
                'correct'   => $stats->correct ?? 0,
                'attempted' => $stats->attempted ?? 0,
                'weakCount' => $weakCount,
            ];
        }

        return view('academic-head.subject-performance', compact('user', 'subjectData'));
    }

    // ── Teacher Effectiveness ────────────────────────────────────────────────
    public function teacherEffectiveness()
    {
        [$user, $instituteId, $subjects] = $this->base();

        $teachers = DB::table('users')
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')
            ->where('is_active', 1)
            ->get();

        $teacherData = [];
        foreach ($teachers as $t) {
            $batchIds  = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id');
            $subjectId = $t->primary_subject_id;
            $subjectName = $subjects->firstWhere('id', $subjectId)?->name ?? '—';

            if (!$subjectId || $batchIds->isEmpty()) {
                $teacherData[] = ['teacher' => $t, 'subjectName' => $subjectName, 'classAvg' => 0, 'weakTopics' => 0, 'students' => 0, 'score' => 0];
                continue;
            }

            $nodeIds = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subjectId)
                ->pluck('id');

            $studentIds = DB::table('students')
                ->where('institute_id', $instituteId)
                ->whereIn('batch_id', $batchIds)
                ->where('is_active', 1)
                ->pluck('id');

            $classAvg = $nodeIds->isNotEmpty() && $studentIds->isNotEmpty()
                ? (int) round(DB::table('student_subtopic_mastery')
                    ->whereIn('student_id', $studentIds)
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->avg('mastery_percentage') ?? 0)
                : 0;

            $weakTopics = $nodeIds->isNotEmpty() && $studentIds->isNotEmpty()
                ? DB::table('student_subtopic_mastery')
                    ->whereIn('student_id', $studentIds)
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->groupBy('curriculum_node_id')
                    ->havingRaw('AVG(mastery_percentage) < 40')
                    ->count(DB::raw('DISTINCT curriculum_node_id'))
                : 0;

            // Effectiveness score: classAvg - (weakTopics * 2), clamped 0-100
            $score = (int) max(0, min(100, $classAvg - ($weakTopics * 2)));

            $teacherData[] = [
                'teacher'     => $t,
                'subjectName' => $subjectName,
                'classAvg'    => $classAvg,
                'weakTopics'  => $weakTopics,
                'students'    => $studentIds->count(),
                'score'       => $score,
            ];
        }

        usort($teacherData, fn($a, $b) => $b['score'] - $a['score']);

        $avgEffectiveness = $teacherData
            ? (int) round(collect($teacherData)->avg('score'))
            : 0;

        return view('academic-head.teacher-effectiveness', compact(
            'user', 'teacherData', 'avgEffectiveness'
        ));
    }

    // ── Teacher Assignments (read-only) ──────────────────────────────────────
    public function teacherAssignments()
    {
        [$user, $instituteId, $subjects] = $this->base();

        $teachers = DB::table('users')
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')
            ->where('is_active', 1)
            ->get();

        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        // For each teacher, their batch + subject assignments
        $assignments = [];
        foreach ($teachers as $t) {
            $batchIds   = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id')->toArray();
            $subjectIds = DB::table('user_subject_assignments')->where('user_id', $t->id)->pluck('subject_id')->toArray();
            $assignments[$t->id] = ['batches' => $batchIds, 'subjects' => $subjectIds];
        }

        return view('academic-head.teacher-assignments', compact(
            'user', 'teachers', 'batches', 'subjects', 'assignments'
        ));
    }

    // ── At-Risk Students ─────────────────────────────────────────────────────
    public function atRiskStudents(Request $request)
    {
        [$user, $instituteId, $subjects] = $this->base();

        $batchFilter   = $request->get('batch_id', '');
        $subjectFilter = $request->get('subject_id', '');
        $search        = $request->get('search', '');

        $batches = DB::table('batches')->where('institute_id', $instituteId)->orderBy('name')->get();

        $allNodeIds = DB::table('curriculum_nodes')
            ->where('institute_id', $instituteId)
            ->when($subjectFilter, fn($q) => $q->where('subject_id', $subjectFilter))
            ->pluck('id');

        $studentQuery = DB::table('students as s')
            ->join('batches as b', 'b.id', '=', 's.batch_id')
            ->where('s.institute_id', $instituteId)
            ->where('s.is_active', 1);

        if ($batchFilter) $studentQuery->where('s.batch_id', $batchFilter);
        if ($search) {
            $studentQuery->where(function ($q) use ($search) {
                $q->where('s.name', 'like', "%$search%")->orWhere('s.roll_number', 'like', "%$search%");
            });
        }

        $allStudents = $studentQuery->select('s.id', 's.name', 's.roll_number', 'b.name as batch_name', 's.batch_id')->get();

        // Compute avg mastery per student
        $masteryMap = DB::table('student_subtopic_mastery')
            ->whereIn('student_id', $allStudents->pluck('id'))
            ->whereIn('curriculum_node_id', $allNodeIds)
            ->groupBy('student_id')
            ->selectRaw('student_id, AVG(mastery_percentage) as avg_m, COUNT(DISTINCT curriculum_node_id) as topics')
            ->get()
            ->keyBy('student_id');

        $atRisk = $allStudents->map(function ($s) use ($masteryMap) {
            $m = $masteryMap->get($s->id);
            $s->avg_m  = $m ? (int) round($m->avg_m) : 0;
            $s->topics = $m ? $m->topics : 0;
            return $s;
        })->filter(fn($s) => $s->avg_m < 40 && $s->topics > 0)
          ->sortBy('avg_m')
          ->values();

        return view('academic-head.at-risk-students', compact(
            'user', 'subjects', 'batches', 'atRisk',
            'batchFilter', 'subjectFilter', 'search'
        ));
    }

    // ── Notifications ────────────────────────────────────────────────────────
    public function notifications()
    {
        [$user, $instituteId] = $this->base();
        $notifications = DB::table('notifications')
            ->where('institute_id', $instituteId)
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();
        return view('academic-head.notifications', compact('user', 'notifications'));
    }

    // ── Teacher Deep-dive ────────────────────────────────────────────────────
    public function teacherDeepDive($teacherId)
    {
        [$user, $instituteId, $subjects] = $this->base();

        $teacher = DB::table('users')
            ->where('id', $teacherId)
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')
            ->first();
        abort_if(!$teacher, 404);

        $subjectId   = $teacher->primary_subject_id;
        $subjectName = $subjects->firstWhere('id', $subjectId)?->name ?? '—';
        $subjectCode = $subjects->firstWhere('id', $subjectId)?->code ?? '?';

        $batchIds = DB::table('user_batch_assignments')
            ->where('user_id', $teacherId)->pluck('batch_id');

        $batches = DB::table('batches')
            ->whereIn('id', $batchIds)->orderBy('name')->get();

        $nodeIds = $subjectId
            ? DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subjectId)->pluck('id')
            : collect();

        $studentIds = DB::table('students')
            ->where('institute_id', $instituteId)
            ->whereIn('batch_id', $batchIds)
            ->where('is_active', 1)->pluck('id');

        // ── Class avg mastery ───────────────────────────────────────────
        $classAvg = ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty())
            ? (int) round(DB::table('student_subtopic_mastery')
                ->whereIn('student_id', $studentIds)
                ->whereIn('curriculum_node_id', $nodeIds)
                ->avg('mastery_percentage') ?? 0)
            : 0;

        // ── Institute-wide avg per topic (for comparison) ───────────────
        $instituteAvgMap = DB::table('student_subtopic_mastery')
            ->whereIn('curriculum_node_id', $nodeIds)
            ->groupBy('curriculum_node_id')
            ->selectRaw('curriculum_node_id, AVG(mastery_percentage) as inst_avg')
            ->get()->keyBy('curriculum_node_id');

        $instituteOverallAvg = $instituteAvgMap->avg('inst_avg') ?? 0;

        // ── Topics: teacher's class avg per topic ───────────────────────
        $topicRows = collect();
        if ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty()) {
            $topicRows = DB::table('student_subtopic_mastery as m')
                ->join('curriculum_nodes as cn', 'cn.id', '=', 'm.curriculum_node_id')
                ->whereIn('m.student_id', $studentIds)
                ->whereIn('m.curriculum_node_id', $nodeIds)
                ->groupBy('m.curriculum_node_id', 'cn.name', 'cn.code')
                ->selectRaw('m.curriculum_node_id, cn.name as topic_name, cn.code as topic_code,
                             AVG(m.mastery_percentage) as avg_m,
                             SUM(m.total_questions_correct) as correct,
                             SUM(m.total_questions_attempted) as attempted')
                ->get()
                ->map(function ($t) use ($instituteAvgMap) {
                    $t->inst_avg = round($instituteAvgMap->get($t->curriculum_node_id)?->inst_avg ?? 0, 1);
                    $t->diff     = round($t->avg_m - $t->inst_avg, 1);
                    return $t;
                });
        }

        $strongTopics = $topicRows->sortByDesc('avg_m')->take(5)->values();
        $weakTopics   = $topicRows->sortBy('avg_m')->take(5)->values();
        $weakCount    = $topicRows->filter(fn($t) => $t->avg_m < 40)->count();
        $strongCount  = $topicRows->filter(fn($t) => $t->avg_m >= 70)->count();

        // ── At-risk students (avg mastery < 40%) ───────────────────────
        $atRiskCount = 0;
        if ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty()) {
            $atRiskCount = DB::table('student_subtopic_mastery')
                ->whereIn('student_id', $studentIds)
                ->whereIn('curriculum_node_id', $nodeIds)
                ->groupBy('student_id')
                ->havingRaw('AVG(mastery_percentage) < 40')
                ->count(DB::raw('DISTINCT student_id'));
        }

        $atRiskPct = $studentIds->count() > 0
            ? (int) round($atRiskCount / $studentIds->count() * 100)
            : 0;

        // ── Batch-wise performance ──────────────────────────────────────
        $batchPerformance = [];
        foreach ($batches as $b) {
            $bStudentIds = DB::table('students')
                ->where('institute_id', $instituteId)
                ->where('batch_id', $b->id)
                ->where('is_active', 1)->pluck('id');

            $bAvg = ($nodeIds->isNotEmpty() && $bStudentIds->isNotEmpty())
                ? (int) round(DB::table('student_subtopic_mastery')
                    ->whereIn('student_id', $bStudentIds)
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->avg('mastery_percentage') ?? 0)
                : 0;

            $bWeak = ($nodeIds->isNotEmpty() && $bStudentIds->isNotEmpty())
                ? DB::table('student_subtopic_mastery')
                    ->whereIn('student_id', $bStudentIds)
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->groupBy('curriculum_node_id')
                    ->havingRaw('AVG(mastery_percentage) < 40')
                    ->count(DB::raw('DISTINCT curriculum_node_id'))
                : 0;

            $bAtRisk = ($nodeIds->isNotEmpty() && $bStudentIds->isNotEmpty())
                ? DB::table('student_subtopic_mastery')
                    ->whereIn('student_id', $bStudentIds)
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->groupBy('student_id')
                    ->havingRaw('AVG(mastery_percentage) < 40')
                    ->count(DB::raw('DISTINCT student_id'))
                : 0;

            $batchPerformance[] = [
                'batch'      => $b,
                'avg'        => $bAvg,
                'weakTopics' => $bWeak,
                'atRisk'     => $bAtRisk,
                'students'   => $bStudentIds->count(),
            ];
        }

        // ── Test performance trend (last 8 tests) ─────────────────────
        $testTrend = [];
        $recentTests = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->orderByDesc('test_date')
            ->limit(8)->get()->reverse()->values();

        foreach ($recentTests as $t) {
            $teacherAvg = $studentIds->isNotEmpty()
                ? (int) round(DB::table('test_results_cache')
                    ->where('test_id', $t->id)
                    ->whereIn('student_id', $studentIds)
                    ->avg('total_marks') ?? 0)
                : 0;

            $instAvg = (int) round(DB::table('test_results_cache')
                ->where('test_id', $t->id)
                ->avg('total_marks') ?? 0);

            $maxMarks = $t->total_questions * 4;
            $testTrend[] = [
                'code'         => $t->test_code,
                'date'         => $t->test_date,
                'teacherAvg'   => $teacherAvg,
                'instAvg'      => $instAvg,
                'maxMarks'     => $maxMarks,
                'teacherPct'   => $maxMarks > 0 ? (int) round($teacherAvg / $maxMarks * 100) : 0,
                'instPct'      => $maxMarks > 0 ? (int) round($instAvg    / $maxMarks * 100) : 0,
            ];
        }

        // ── Effectiveness score (weighted formula) ──────────────────────
        $growthTrend = count($testTrend) >= 2
            ? (end($testTrend)['teacherPct'] - $testTrend[0]['teacherPct'])
            : 0;
        $growthScore = (int) min(100, max(0, 50 + $growthTrend));

        $effectivenessScore = (int) min(100, max(0,
            0.35 * $classAvg +
            0.25 * max(0, 100 - $weakCount * 5) +
            0.20 * $growthScore +
            0.20 * (100 - $atRiskPct)
        ));

        // ── Rank among all teachers ─────────────────────────────────────
        $allTeachers = DB::table('users')
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')->where('is_active', 1)->get();

        $allScores = [];
        foreach ($allTeachers as $t) {
            $tBatches  = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id');
            $tSubject  = $t->primary_subject_id;
            $tNodes    = $tSubject ? DB::table('curriculum_nodes')->where('institute_id', $instituteId)->where('subject_id', $tSubject)->pluck('id') : collect();
            $tStudents = DB::table('students')->where('institute_id', $instituteId)->whereIn('batch_id', $tBatches)->where('is_active', 1)->pluck('id');

            $tAvg = ($tNodes->isNotEmpty() && $tStudents->isNotEmpty())
                ? (int) round(DB::table('student_subtopic_mastery')->whereIn('student_id', $tStudents)->whereIn('curriculum_node_id', $tNodes)->avg('mastery_percentage') ?? 0)
                : 0;
            $tWeak = ($tNodes->isNotEmpty() && $tStudents->isNotEmpty())
                ? DB::table('student_subtopic_mastery')->whereIn('student_id', $tStudents)->whereIn('curriculum_node_id', $tNodes)->groupBy('curriculum_node_id')->havingRaw('AVG(mastery_percentage) < 40')->count(DB::raw('DISTINCT curriculum_node_id'))
                : 0;
            $tAtRisk = ($tNodes->isNotEmpty() && $tStudents->isNotEmpty())
                ? DB::table('student_subtopic_mastery')->whereIn('student_id', $tStudents)->whereIn('curriculum_node_id', $tNodes)->groupBy('student_id')->havingRaw('AVG(mastery_percentage) < 40')->count(DB::raw('DISTINCT student_id'))
                : 0;
            $tAtRiskPct = $tStudents->count() > 0 ? (int) round($tAtRisk / $tStudents->count() * 100) : 0;

            $allScores[$t->id] = max(0, min(100, (int) round(
                0.35 * $tAvg +
                0.25 * max(0, 100 - $tWeak * 5) +
                0.20 * 50 +  // growth score not available here without test history; use neutral 50
                0.20 * (100 - $tAtRiskPct)
            )));
        }
        arsort($allScores);
        $rank = array_search($teacherId, array_keys($allScores)) + 1;
        $totalTeachers = count($allScores);

        // ── AI Action Items ─────────────────────────────────────────────
        $actions = [];
        if ($weakCount > 0) {
            $worstTopic = $weakTopics->first();
            $actions[] = "Focus remedial sessions on <strong>{$worstTopic->topic_name}</strong> — class average only " . (int)round($worstTopic->avg_m) . "% vs institute avg " . (int)round($worstTopic->inst_avg) . "%.";
        }
        if ($atRiskCount > 0) {
            $actions[] = "{$atRiskCount} student" . ($atRiskCount > 1 ? 's' : '') . " below 40% mastery threshold. Schedule one-on-one sessions.";
        }
        if ($classAvg < (int) round($instituteOverallAvg)) {
            $actions[] = "Class average (" . $classAvg . "%) is below institute average (" . (int)round($instituteOverallAvg) . "%). Review teaching pace.";
        }
        if ($strongCount > 0 && $weakCount > 0) {
            $actions[] = "Strong topics identified — consider peer teaching: high-mastery students can assist weak-topic groups.";
        }
        if (empty($actions)) {
            $actions[] = "Performance is on track. Maintain current teaching velocity and continue topic-level assessments.";
        }

        return view('academic-head.teacher-deep-dive', compact(
            'user', 'teacher', 'subjectName', 'subjectCode', 'batches',
            'classAvg', 'effectivenessScore', 'rank', 'totalTeachers',
            'studentIds', 'weakCount', 'strongCount', 'atRiskCount', 'atRiskPct',
            'strongTopics', 'weakTopics', 'batchPerformance', 'testTrend',
            'instituteOverallAvg', 'actions'
        ));
    }

    // ── Subject Teacher Comparison ───────────────────────────────────────────
    public function subjectComparison($subjectId)
    {
        [$user, $instituteId, $subjects] = $this->base();

        $subject = DB::table('subjects')
            ->where('id', $subjectId)
            ->where('institute_id', $instituteId)
            ->first();
        abort_if(!$subject, 404);

        // All teachers for this subject
        $teachers = DB::table('users')
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')
            ->where('is_active', 1)
            ->where('primary_subject_id', $subjectId)
            ->get();

        $nodeIds = DB::table('curriculum_nodes')
            ->where('institute_id', $instituteId)
            ->where('subject_id', $subjectId)
            ->orderBy('code')
            ->get();

        // Institute-wide avg per topic
        $instAvgMap = DB::table('student_subtopic_mastery')
            ->whereIn('curriculum_node_id', $nodeIds->pluck('id'))
            ->groupBy('curriculum_node_id')
            ->selectRaw('curriculum_node_id, AVG(mastery_percentage) as inst_avg')
            ->get()->keyBy('curriculum_node_id');

        // Build per-teacher data
        $teacherData = [];
        foreach ($teachers as $t) {
            $batchIds   = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id');
            $batchObjs  = DB::table('batches')->whereIn('id', $batchIds)->get();
            $studentIds = DB::table('students')
                ->where('institute_id', $instituteId)
                ->whereIn('batch_id', $batchIds)
                ->where('is_active', 1)->pluck('id');

            // Per-topic mastery for this teacher's students
            $topicMastery = collect();
            if ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty()) {
                $topicMastery = DB::table('student_subtopic_mastery')
                    ->whereIn('student_id', $studentIds)
                    ->whereIn('curriculum_node_id', $nodeIds->pluck('id'))
                    ->groupBy('curriculum_node_id')
                    ->selectRaw('curriculum_node_id, AVG(mastery_percentage) as avg_m')
                    ->get()->keyBy('curriculum_node_id');
            }

            $classAvg   = $topicMastery->isNotEmpty() ? (int) round($topicMastery->avg('avg_m')) : 0;
            $weakCount  = $topicMastery->filter(fn($r) => $r->avg_m < 40)->count();
            $strongCount = $topicMastery->filter(fn($r) => $r->avg_m >= 70)->count();

            // Effectiveness score (same formula as teacher-effectiveness)
            $effectScore = (int) min(100, max(0, round(0.35 * $classAvg + 0.25 * max(0, 100 - $weakCount * 5) + 0.20 * 50 + 0.20 * 100)));

            $teacherData[] = [
                'teacher'      => $t,
                'batchIds'     => $batchIds->toArray(),
                'batches'      => $batchObjs,
                'studentIds'   => $studentIds,
                'studentCount' => $studentIds->count(),
                'topicMastery' => $topicMastery,
                'classAvg'     => $classAvg,
                'weakCount'    => $weakCount,
                'strongCount'  => $strongCount,
                'effectScore'  => $effectScore,
            ];
        }

        // Sort by class avg desc, take top 2 for comparison
        usort($teacherData, fn($a, $b) => $b['classAvg'] - $a['classAvg']);
        $compareTeachers = array_slice($teacherData, 0, 2);
        $otherTeachers   = array_slice($teacherData, 2);

        // Institute-wide totals for banner
        $allStudentIds = DB::table('students')
            ->where('institute_id', $instituteId)->where('is_active', 1)->pluck('id');

        $instClassAvg = $nodeIds->isNotEmpty()
            ? (int) round(DB::table('student_subtopic_mastery')
                ->whereIn('curriculum_node_id', $nodeIds->pluck('id'))
                ->avg('mastery_percentage') ?? 0)
            : 0;
        $instWeakCount = $nodeIds->isNotEmpty()
            ? DB::table('student_subtopic_mastery')
                ->whereIn('curriculum_node_id', $nodeIds->pluck('id'))
                ->groupBy('curriculum_node_id')
                ->havingRaw('AVG(mastery_percentage) < 40')
                ->count(DB::raw('DISTINCT curriculum_node_id'))
            : 0;
        $instStrongCount = $nodeIds->isNotEmpty()
            ? DB::table('student_subtopic_mastery')
                ->whereIn('curriculum_node_id', $nodeIds->pluck('id'))
                ->groupBy('curriculum_node_id')
                ->havingRaw('AVG(mastery_percentage) >= 70')
                ->count(DB::raw('DISTINCT curriculum_node_id'))
            : 0;
        $atRiskCount = 0;
        if ($nodeIds->isNotEmpty() && $allStudentIds->isNotEmpty()) {
            $atRiskCount = DB::table('student_subtopic_mastery')
                ->whereIn('curriculum_node_id', $nodeIds->pluck('id'))
                ->whereIn('student_id', $allStudentIds)
                ->groupBy('student_id')
                ->havingRaw('AVG(mastery_percentage) < 40')
                ->count(DB::raw('DISTINCT student_id'));
        }

        // Build topic comparison rows (only topics that both teachers have data for)
        $topicRows = [];
        foreach ($nodeIds as $node) {
            $vals = [];
            foreach ($compareTeachers as $td) {
                $vals[] = $td['topicMastery']->get($node->id)?->avg_m ?? null;
            }
            $topicRows[] = [
                'node'   => $node,
                'values' => $vals,
                'instAvg'=> round($instAvgMap->get($node->id)?->inst_avg ?? 0, 1),
            ];
        }

        // Add gap + winner to each row
        foreach ($topicRows as &$row) {
            if (count($row['values']) >= 2 && $row['values'][0] !== null && $row['values'][1] !== null) {
                $row['gap']    = round($row['values'][0] - $row['values'][1], 1);
                $row['winner'] = abs($row['gap']) < 3 ? 'tie' : ($row['gap'] > 0 ? 0 : 1);
            } else {
                $row['gap']    = null;
                $row['winner'] = null;
            }
        }
        unset($row);

        // Comparison summary chips
        $chips = [];
        if (count($compareTeachers) >= 2) {
            $t0 = $compareTeachers[0]; $t1 = $compareTeachers[1];
            $diff = $t0['classAvg'] - $t1['classAvg'];
            $chips[] = ['label' => 'Best Overall',         'value' => $t0['teacher']->name . ' (+' . abs($diff) . 'pp)', 'color' => '#7fb685'];
            $chips[] = ['label' => 'More Strong Topics',   'value' => $t0['teacher']->name . ': ' . $t0['strongCount'] . ' vs ' . $t1['teacher']->name . ': ' . $t1['strongCount'], 'color' => '#7a95c8'];
            $winWeak = $t0['weakCount'] <= $t1['weakCount'] ? $t0 : $t1;
            $chips[] = ['label' => 'Fewer Weak Topics',    'value' => $winWeak['teacher']->name . ': ' . $winWeak['weakCount'] . ' vs ' . ($winWeak === $t0 ? $t1['teacher']->name.': '.$t1['weakCount'] : $t0['teacher']->name.': '.$t0['weakCount']), 'color' => '#d4a574'];
            $winEff  = $t0['effectScore'] >= $t1['effectScore'] ? $t0 : $t1;
            $chips[] = ['label' => 'Better Effectiveness', 'value' => $winEff['teacher']->name . ': ' . $winEff['effectScore'] . ' vs ' . ($winEff === $t0 ? $t1['teacher']->name.': '.$t1['effectScore'] : $t0['teacher']->name.': '.$t0['effectScore']), 'color' => '#7fb685'];
        }

        // AI Cross-teacher recommendations
        $aiRecs = [];
        if (count($compareTeachers) >= 2) {
            $t0 = $compareTeachers[0]; $t1 = $compareTeachers[1];
            $t0WinTopics = collect($topicRows)->filter(fn($r) => isset($r['winner']) && $r['winner'] === 0)->take(2);
            $t1WinTopics = collect($topicRows)->filter(fn($r) => isset($r['winner']) && $r['winner'] === 1)->take(2);

            if ($t0WinTopics->isNotEmpty()) {
                $names = $t0WinTopics->map(fn($r) => $r['node']->name)->implode(', ');
                $aiRecs[] = "<strong>{$t0['teacher']->name}</strong> outperforms in: {$names}. Consider having them conduct revision sessions for the other batches.";
            }
            if ($t1WinTopics->isNotEmpty()) {
                $names = $t1WinTopics->map(fn($r) => $r['node']->name)->implode(', ');
                $aiRecs[] = "<strong>{$t1['teacher']->name}</strong> leads in: {$names}. Cross-batch knowledge sharing recommended.";
            }
            $bigGaps = collect($topicRows)->filter(fn($r) => isset($r['gap']) && abs($r['gap']) >= 15);
            if ($bigGaps->count() > 0) {
                $aiRecs[] = $bigGaps->count() . ' topics show a gap of 15pp+. Schedule a joint analysis session between both teachers to align teaching strategies.';
            }
            if (empty($aiRecs)) {
                $aiRecs[] = 'Both teachers are performing similarly. Maintain consistent assessment frequency and focus on remaining weak topics together.';
            }
        }

        return view('academic-head.subject-comparison', compact(
            'user', 'subject', 'teachers', 'compareTeachers', 'otherTeachers',
            'nodeIds', 'topicRows', 'instAvgMap',
            'instClassAvg', 'instWeakCount', 'instStrongCount', 'atRiskCount',
            'chips', 'aiRecs'
        ));
    }

    // ── Help ─────────────────────────────────────────────────────────────────
    public function help()
    {
        [$user] = $this->base();
        return view('academic-head.help', compact('user'));
    }
}
