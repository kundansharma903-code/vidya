<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OwnerController extends Controller
{
    private function base(): array
    {
        $user        = Auth::user();
        $instituteId = $user->institute_id;
        $subjects    = DB::table('subjects')->where('institute_id', $instituteId)->where('is_active', 1)->orderBy('name')->get();
        return [$user, $instituteId, $subjects];
    }

    private function financialBase(int $instituteId): array
    {
        $batches  = DB::table('batches')->where('institute_id', $instituteId)->where('is_active', 1)->get();
        $teachers = DB::table('users')->where('institute_id', $instituteId)->where('role', 'teacher')->where('is_active', 1)->get();

        $monthlyRevenue = 0;
        foreach ($batches as $b) {
            $count = DB::table('students')->where('batch_id', $b->id)->where('is_active', 1)->count();
            $monthlyRevenue += ($b->monthly_fee ?? 0) * $count;
        }

        $monthlyCost = DB::table('users')
            ->where('institute_id', $instituteId)
            ->whereIn('role', ['teacher', 'sub_admin', 'typist', 'academic_head'])
            ->where('is_active', 1)
            ->sum('monthly_salary');

        return [$batches, $teachers, $monthlyRevenue, $monthlyCost];
    }

    // ── Course Performance ───────────────────────────────────────────────────
    public function coursePerformance()
    {
        [$user, $instituteId, $subjects] = $this->base();

        $courses = DB::table('courses')
            ->where('institute_id', $instituteId)
            ->where('is_active', 1)
            ->get();

        [, , $monthlyRevenue, $monthlyCost] = $this->financialBase($instituteId);

        $courseData = [];
        foreach ($courses as $course) {
            $batches = DB::table('batches')
                ->where('institute_id', $instituteId)
                ->where('course_id', $course->id)
                ->where('is_active', 1)
                ->get();

            $totalStudents = 0;
            $courseRevenue = 0;
            foreach ($batches as $b) {
                $cnt = DB::table('students')->where('batch_id', $b->id)->where('is_active', 1)->count();
                $totalStudents += $cnt;
                $courseRevenue += ($b->monthly_fee ?? 0) * $cnt * 12;
            }

            $subjectIds = DB::table('subjects')
                ->where('institute_id', $instituteId)
                ->where('course_id', $course->id)
                ->pluck('id');

            $nodeIds = $subjectIds->isNotEmpty()
                ? DB::table('curriculum_nodes')->where('institute_id', $instituteId)->whereIn('subject_id', $subjectIds)->pluck('id')
                : collect();

            $classAvg = $nodeIds->isNotEmpty()
                ? (int) round(DB::table('student_subtopic_mastery')->whereIn('curriculum_node_id', $nodeIds)->avg('mastery_percentage') ?? 0)
                : 0;

            $totalNodes   = $nodeIds->count();
            $coveredNodes = $nodeIds->isNotEmpty()
                ? DB::table('student_subtopic_mastery')->whereIn('curriculum_node_id', $nodeIds)->distinct('curriculum_node_id')->count('curriculum_node_id')
                : 0;

            $courseData[] = [
                'course'        => $course,
                'batches'       => $batches,
                'totalStudents' => $totalStudents,
                'annualRevenue' => $courseRevenue,
                'classAvg'      => $classAvg,
                'totalNodes'    => $totalNodes,
                'coveredNodes'  => $coveredNodes,
                'coveragePct'   => $totalNodes > 0 ? (int) round($coveredNodes / $totalNodes * 100) : 0,
            ];
        }

        usort($courseData, fn($a, $b) => $b['annualRevenue'] - $a['annualRevenue']);

        $totalAnnualRevenue = collect($courseData)->sum('annualRevenue');
        $totalAnnualCost    = $monthlyCost * 12;

        return view('owner.course-performance', compact(
            'user', 'courseData', 'totalAnnualRevenue', 'totalAnnualCost'
        ));
    }

    // ── Subject ROI List ─────────────────────────────────────────────────────
    public function subjectRoi()
    {
        [$user, $instituteId, $subjects] = $this->base();
        [, $teachers, $monthlyRevenue, $monthlyCost] = $this->financialBase($instituteId);

        $subjectData = [];
        foreach ($subjects as $subj) {
            $subjectTeachers = $teachers->where('primary_subject_id', $subj->id);

            $teacherRevenue = 0;
            $teacherSalary  = 0;
            $teacherStudents = 0;
            foreach ($subjectTeachers as $t) {
                $batchIds = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id');
                $students = DB::table('students')->where('institute_id', $instituteId)->whereIn('batch_id', $batchIds)->where('is_active', 1)->count();
                foreach ($batchIds as $bid) {
                    $fee = DB::table('batches')->where('id', $bid)->value('monthly_fee') ?? 0;
                    $cnt = DB::table('students')->where('batch_id', $bid)->where('is_active', 1)->count();
                    $teacherRevenue += $fee * $cnt * 12;
                }
                $teacherSalary  += ($t->monthly_salary ?? 0) * 12;
                $teacherStudents += $students;
            }

            $nodeIds  = DB::table('curriculum_nodes')->where('institute_id', $instituteId)->where('subject_id', $subj->id)->pluck('id');
            $classAvg = $nodeIds->isNotEmpty()
                ? (int) round(DB::table('student_subtopic_mastery')->whereIn('curriculum_node_id', $nodeIds)->avg('mastery_percentage') ?? 0)
                : 0;

            $roi = $teacherSalary > 0 ? round($teacherRevenue / $teacherSalary, 1) : 0;
            $net = $teacherRevenue - $teacherSalary;

            $subjectData[] = [
                'subject'        => $subj,
                'teachers'       => $subjectTeachers->values(),
                'annualRevenue'  => $teacherRevenue,
                'annualSalary'   => $teacherSalary,
                'netContribution'=> $net,
                'roi'            => $roi,
                'students'       => $teacherStudents,
                'classAvg'       => $classAvg,
            ];
        }

        usort($subjectData, fn($a, $b) => $b['roi'] <=> $a['roi']);

        return view('owner.subject-roi', compact('user', 'subjectData'));
    }

    // ── Subject ROI Detail ───────────────────────────────────────────────────
    public function subjectRoiDetail($subjectId)
    {
        [$user, $instituteId, $subjects] = $this->base();

        $subject = DB::table('subjects')->where('id', $subjectId)->where('institute_id', $instituteId)->first();
        abort_if(!$subject, 404);

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

        $instAvgMap = DB::table('student_subtopic_mastery')
            ->whereIn('curriculum_node_id', $nodeIds->pluck('id'))
            ->groupBy('curriculum_node_id')
            ->selectRaw('curriculum_node_id, AVG(mastery_percentage) as inst_avg')
            ->get()->keyBy('curriculum_node_id');

        // Institute-wide subject stats
        $instClassAvg = $nodeIds->isNotEmpty()
            ? (int) round(DB::table('student_subtopic_mastery')->whereIn('curriculum_node_id', $nodeIds->pluck('id'))->avg('mastery_percentage') ?? 0)
            : 0;

        // Per-teacher breakdown
        $teacherData = [];
        foreach ($teachers as $t) {
            $batchIds   = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id');
            $batchObjs  = DB::table('batches')->whereIn('id', $batchIds)->get();
            $studentIds = DB::table('students')->where('institute_id', $instituteId)->whereIn('batch_id', $batchIds)->where('is_active', 1)->pluck('id');

            $annualRevenue = 0;
            foreach ($batchIds as $bid) {
                $fee = DB::table('batches')->where('id', $bid)->value('monthly_fee') ?? 0;
                $cnt = DB::table('students')->where('batch_id', $bid)->where('is_active', 1)->count();
                $annualRevenue += $fee * $cnt * 12;
            }
            $annualSalary = ($t->monthly_salary ?? 0) * 12;
            $roi          = $annualSalary > 0 ? round($annualRevenue / $annualSalary, 1) : 0;
            $net          = $annualRevenue - $annualSalary;

            $topicMastery = $nodeIds->isNotEmpty() && $studentIds->isNotEmpty()
                ? DB::table('student_subtopic_mastery')
                    ->whereIn('student_id', $studentIds)
                    ->whereIn('curriculum_node_id', $nodeIds->pluck('id'))
                    ->groupBy('curriculum_node_id')
                    ->selectRaw('curriculum_node_id, AVG(mastery_percentage) as avg_m')
                    ->get()->keyBy('curriculum_node_id')
                : collect();

            $classAvg   = $topicMastery->isNotEmpty() ? (int) round($topicMastery->avg('avg_m')) : 0;
            $weakCount  = $topicMastery->filter(fn($r) => $r->avg_m < 40)->count();
            $strongCount = $topicMastery->filter(fn($r) => $r->avg_m >= 70)->count();
            $effectScore = (int) min(100, max(0, round(0.35 * $classAvg + 0.25 * max(0, 100 - $weakCount * 5) + 0.20 * 50 + 0.20 * 100)));

            $teacherData[] = [
                'teacher'       => $t,
                'batches'       => $batchObjs,
                'studentIds'    => $studentIds,
                'studentCount'  => $studentIds->count(),
                'topicMastery'  => $topicMastery,
                'classAvg'      => $classAvg,
                'weakCount'     => $weakCount,
                'strongCount'   => $strongCount,
                'effectScore'   => $effectScore,
                'annualRevenue' => $annualRevenue,
                'annualSalary'  => $annualSalary,
                'netContribution'=> $net,
                'roi'           => $roi,
            ];
        }

        usort($teacherData, fn($a, $b) => $b['classAvg'] - $a['classAvg']);
        $compareTeachers = array_slice($teacherData, 0, 2);

        // Topic comparison rows
        $topicRows = [];
        foreach ($nodeIds as $node) {
            $vals = [];
            foreach ($compareTeachers as $td) {
                $vals[] = round($td['topicMastery']->get($node->id)?->avg_m ?? 0, 1);
            }
            $instAvg = round($instAvgMap->get($node->id)?->inst_avg ?? 0, 1);
            $gap     = count($vals) >= 2 ? round($vals[0] - $vals[1], 1) : null;
            $winner  = $gap !== null ? (abs($gap) < 3 ? 'tie' : ($gap > 0 ? 0 : 1)) : null;
            $topicRows[] = ['node' => $node, 'values' => $vals, 'instAvg' => $instAvg, 'gap' => $gap, 'winner' => $winner];
        }

        // Decision chips & AI recs
        $chips = [];
        $aiRecs = [];
        if (count($compareTeachers) >= 2) {
            $t0 = $compareTeachers[0]; $t1 = $compareTeachers[1];
            $chips[] = ['label' => 'Best ROI',       'value' => ($t0['roi'] >= $t1['roi'] ? $t0['teacher']->name : $t1['teacher']->name) . ': ' . max($t0['roi'],$t1['roi']) . 'x', 'color' => '#7fb685'];
            $chips[] = ['label' => 'Best Academic',  'value' => $t0['teacher']->name . ': ' . $t0['classAvg'] . '% avg', 'color' => '#7a95c8'];
            $chips[] = ['label' => 'Cost Diff',      'value' => '₹' . number_format(abs($t0['annualSalary'] - $t1['annualSalary'])) . '/yr', 'color' => '#d4a574'];
            $chips[] = ['label' => 'Revenue Diff',   'value' => '₹' . number_format(abs($t0['annualRevenue'] - $t1['annualRevenue'])) . '/yr', 'color' => '#a392c8'];

            if ($t0['roi'] > $t1['roi'] * 1.5) {
                $aiRecs[] = "<strong>{$t0['teacher']->name}</strong> delivers {$t0['roi']}x ROI vs {$t1['teacher']->name}'s {$t1['roi']}x. Consider reallocating batch load to maximise returns.";
            }
            $bigGaps = collect($topicRows)->filter(fn($r) => $r['gap'] !== null && abs($r['gap']) >= 15);
            if ($bigGaps->count() > 0) {
                $aiRecs[] = $bigGaps->count() . " topics show 15pp+ gap between teachers. Joint session or coaching intervention recommended.";
            }
            if ($t0['classAvg'] < $instClassAvg - 5 && $t1['classAvg'] < $instClassAvg - 5) {
                $aiRecs[] = "Both teachers are below institute average ({$instClassAvg}%). Subject-wide intervention may be needed.";
            }
            if (empty($aiRecs)) {
                $aiRecs[] = "Both teachers are performing within acceptable range. Monitor monthly for sustained growth.";
            }
        }

        $atRiskCount = 0;
        $allStudentIds = DB::table('students')->where('institute_id', $instituteId)->where('is_active', 1)->pluck('id');
        if ($nodeIds->isNotEmpty() && $allStudentIds->isNotEmpty()) {
            $atRiskCount = DB::table('student_subtopic_mastery')
                ->whereIn('curriculum_node_id', $nodeIds->pluck('id'))
                ->whereIn('student_id', $allStudentIds)
                ->groupBy('student_id')
                ->havingRaw('AVG(mastery_percentage) < 40')
                ->pluck('student_id')->count();
        }
        $instWeakCount   = $nodeIds->isNotEmpty()
            ? DB::table('student_subtopic_mastery')->whereIn('curriculum_node_id', $nodeIds->pluck('id'))->groupBy('curriculum_node_id')->havingRaw('AVG(mastery_percentage) < 40')->pluck('curriculum_node_id')->count()
            : 0;
        $instStrongCount = $nodeIds->isNotEmpty()
            ? DB::table('student_subtopic_mastery')->whereIn('curriculum_node_id', $nodeIds->pluck('id'))->groupBy('curriculum_node_id')->havingRaw('AVG(mastery_percentage) >= 70')->pluck('curriculum_node_id')->count()
            : 0;

        return view('owner.subject-roi-detail', compact(
            'user', 'subject', 'teacherData', 'compareTeachers',
            'nodeIds', 'topicRows', 'instClassAvg', 'instWeakCount', 'instStrongCount', 'atRiskCount',
            'chips', 'aiRecs'
        ));
    }

    // ── Financial Summary ────────────────────────────────────────────────────
    public function financial()
    {
        [$user, $instituteId] = $this->base();
        [$batches, $teachers, $monthlyRevenue, $monthlyCost] = $this->financialBase($instituteId);

        $annualRevenue = $monthlyRevenue * 12;
        $annualCost    = $monthlyCost * 12;
        $annualProfit  = $annualRevenue - $annualCost;
        $margin        = $annualRevenue > 0 ? (int) round($annualProfit / $annualRevenue * 100) : 0;

        // Per-batch revenue breakdown
        $batchBreakdown = [];
        foreach ($batches as $b) {
            $cnt = DB::table('students')->where('batch_id', $b->id)->where('is_active', 1)->count();
            $batchBreakdown[] = [
                'batch'          => $b,
                'students'       => $cnt,
                'monthlyRevenue' => ($b->monthly_fee ?? 0) * $cnt,
                'annualRevenue'  => ($b->monthly_fee ?? 0) * $cnt * 12,
            ];
        }
        usort($batchBreakdown, fn($a, $b) => $b['annualRevenue'] - $a['annualRevenue']);

        // Per-teacher cost breakdown
        $staffBreakdown = DB::table('users')
            ->where('institute_id', $instituteId)
            ->whereIn('role', ['teacher', 'sub_admin', 'typist', 'academic_head'])
            ->where('is_active', 1)
            ->orderByDesc('monthly_salary')
            ->get(['id', 'name', 'role', 'monthly_salary', 'tenure_start']);

        return view('owner.financial', compact(
            'user',
            'monthlyRevenue', 'annualRevenue',
            'monthlyCost', 'annualCost',
            'annualProfit', 'margin',
            'batchBreakdown', 'staffBreakdown'
        ));
    }

    // ── Teacher Performance (with ROI) ───────────────────────────────────────
    public function teachers()
    {
        [$user, $instituteId, $subjects] = $this->base();

        $teachers = DB::table('users')
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')
            ->where('is_active', 1)
            ->get();

        $teacherData = [];
        foreach ($teachers as $t) {
            $batchIds   = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id');
            $subjectId  = $t->primary_subject_id;
            $subjectName = $subjects->firstWhere('id', $subjectId)?->name ?? '—';
            $nodeIds    = $subjectId ? DB::table('curriculum_nodes')->where('institute_id', $instituteId)->where('subject_id', $subjectId)->pluck('id') : collect();
            $studentIds = DB::table('students')->where('institute_id', $instituteId)->whereIn('batch_id', $batchIds)->where('is_active', 1)->pluck('id');

            $classAvg  = ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty())
                ? (int) round(DB::table('student_subtopic_mastery')->whereIn('student_id', $studentIds)->whereIn('curriculum_node_id', $nodeIds)->avg('mastery_percentage') ?? 0)
                : 0;
            $weakCount = ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty())
                ? DB::table('student_subtopic_mastery')->whereIn('student_id', $studentIds)->whereIn('curriculum_node_id', $nodeIds)->groupBy('curriculum_node_id')->havingRaw('AVG(mastery_percentage) < 40')->pluck('curriculum_node_id')->count()
                : 0;
            $score = (int) max(0, min(100, $classAvg - ($weakCount * 2)));

            $annualRevenue = 0;
            foreach ($batchIds as $bid) {
                $fee = DB::table('batches')->where('id', $bid)->value('monthly_fee') ?? 0;
                $cnt = DB::table('students')->where('batch_id', $bid)->where('is_active', 1)->count();
                $annualRevenue += $fee * $cnt * 12;
            }
            $annualSalary = ($t->monthly_salary ?? 0) * 12;
            $roi = $annualSalary > 0 ? round($annualRevenue / $annualSalary, 1) : 0;

            $teacherData[] = [
                'teacher'       => $t,
                'subjectName'   => $subjectName,
                'classAvg'      => $classAvg,
                'weakCount'     => $weakCount,
                'students'      => $studentIds->count(),
                'score'         => $score,
                'annualRevenue' => $annualRevenue,
                'annualSalary'  => $annualSalary,
                'roi'           => $roi,
            ];
        }
        usort($teacherData, fn($a, $b) => $b['roi'] <=> $a['roi']);

        return view('owner.teachers', compact('user', 'teacherData'));
    }

    // ── Teacher Deep-Dive ────────────────────────────────────────────────────
    public function teacherDeepDive($teacherId)
    {
        [$user, $instituteId, $subjects] = $this->base();

        $teacher = DB::table('users')->where('id', $teacherId)->where('institute_id', $instituteId)->where('role', 'teacher')->first();
        abort_if(!$teacher, 404);

        $subjectId   = $teacher->primary_subject_id;
        $subjectName = $subjects->firstWhere('id', $subjectId)?->name ?? '—';
        $batchIds    = DB::table('user_batch_assignments')->where('user_id', $teacherId)->pluck('batch_id');
        $batches     = DB::table('batches')->whereIn('id', $batchIds)->orderBy('name')->get();
        $nodeIds     = $subjectId ? DB::table('curriculum_nodes')->where('institute_id', $instituteId)->where('subject_id', $subjectId)->pluck('id') : collect();
        $studentIds  = DB::table('students')->where('institute_id', $instituteId)->whereIn('batch_id', $batchIds)->where('is_active', 1)->pluck('id');

        $classAvg = ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty())
            ? (int) round(DB::table('student_subtopic_mastery')->whereIn('student_id', $studentIds)->whereIn('curriculum_node_id', $nodeIds)->avg('mastery_percentage') ?? 0)
            : 0;

        $weakCount = ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty())
            ? DB::table('student_subtopic_mastery')->whereIn('student_id', $studentIds)->whereIn('curriculum_node_id', $nodeIds)->groupBy('curriculum_node_id')->havingRaw('AVG(mastery_percentage) < 40')->pluck('curriculum_node_id')->count()
            : 0;
        $strongCount = ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty())
            ? DB::table('student_subtopic_mastery')->whereIn('student_id', $studentIds)->whereIn('curriculum_node_id', $nodeIds)->groupBy('curriculum_node_id')->havingRaw('AVG(mastery_percentage) >= 70')->pluck('curriculum_node_id')->count()
            : 0;

        $atRiskCount = ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty())
            ? DB::table('student_subtopic_mastery')->whereIn('student_id', $studentIds)->whereIn('curriculum_node_id', $nodeIds)->groupBy('student_id')->havingRaw('AVG(mastery_percentage) < 40')->pluck('student_id')->count()
            : 0;
        $atRiskPct = $studentIds->count() > 0 ? (int) round($atRiskCount / $studentIds->count() * 100) : 0;

        $instituteAvgMap = DB::table('student_subtopic_mastery')->whereIn('curriculum_node_id', $nodeIds)->groupBy('curriculum_node_id')->selectRaw('curriculum_node_id, AVG(mastery_percentage) as inst_avg')->get()->keyBy('curriculum_node_id');

        $topicRows = collect();
        if ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty()) {
            $topicRows = DB::table('student_subtopic_mastery as m')
                ->join('curriculum_nodes as cn', 'cn.id', '=', 'm.curriculum_node_id')
                ->whereIn('m.student_id', $studentIds)
                ->whereIn('m.curriculum_node_id', $nodeIds)
                ->groupBy('m.curriculum_node_id', 'cn.name', 'cn.code')
                ->selectRaw('m.curriculum_node_id, cn.name as topic_name, cn.code as topic_code, AVG(m.mastery_percentage) as avg_m')
                ->get()->map(function ($t) use ($instituteAvgMap) {
                    $t->inst_avg = round($instituteAvgMap->get($t->curriculum_node_id)?->inst_avg ?? 0, 1);
                    $t->diff     = round($t->avg_m - $t->inst_avg, 1);
                    return $t;
                });
        }

        $strongTopics = $topicRows->sortByDesc('avg_m')->take(5)->values();
        $weakTopics   = $topicRows->sortBy('avg_m')->take(5)->values();

        // Financial metrics
        $annualRevenue = 0;
        $batchRevenue  = [];
        foreach ($batches as $b) {
            $cnt = DB::table('students')->where('batch_id', $b->id)->where('is_active', 1)->count();
            $rev = ($b->monthly_fee ?? 0) * $cnt * 12;
            $annualRevenue += $rev;
            $batchRevenue[$b->id] = ['batch' => $b, 'students' => $cnt, 'monthly_fee' => $b->monthly_fee ?? 0, 'annual_revenue' => $rev];
        }
        $annualSalary = ($teacher->monthly_salary ?? 0) * 12;
        $roi          = $annualSalary > 0 ? round($annualRevenue / $annualSalary, 1) : 0;
        $netContrib   = $annualRevenue - $annualSalary;
        $tenureYears  = $teacher->tenure_start ? (int) round(\Carbon\Carbon::parse($teacher->tenure_start)->diffInMonths(now()) / 12, 1) : null;

        // Test trend
        $recentTests = DB::table('tests')->where('institute_id', $instituteId)->where('status', 'analyzed')->orderByDesc('test_date')->limit(8)->get()->reverse()->values();
        $testTrend = [];
        foreach ($recentTests as $t) {
            $tAvg    = $studentIds->isNotEmpty() ? (int) round(DB::table('test_results_cache')->where('test_id', $t->id)->whereIn('student_id', $studentIds)->avg('total_marks') ?? 0) : 0;
            $instAvg = (int) round(DB::table('test_results_cache')->where('test_id', $t->id)->avg('total_marks') ?? 0);
            $maxM    = $t->total_questions * 4;
            $testTrend[] = ['code' => $t->test_code, 'date' => $t->test_date, 'teacherPct' => $maxM > 0 ? (int) round($tAvg / $maxM * 100) : 0, 'instPct' => $maxM > 0 ? (int) round($instAvg / $maxM * 100) : 0];
        }

        $growthTrend = count($testTrend) >= 2 ? (end($testTrend)['teacherPct'] - $testTrend[0]['teacherPct']) : 0;
        $growthScore = (int) min(100, max(0, 50 + $growthTrend));
        $effectScore = (int) min(100, max(0, round(0.35 * $classAvg + 0.25 * max(0, 100 - $weakCount * 5) + 0.20 * $growthScore + 0.20 * (100 - $atRiskPct))));

        // Rank
        $allTeachers = DB::table('users')->where('institute_id', $instituteId)->where('role', 'teacher')->where('is_active', 1)->get();
        $allRoi = [];
        foreach ($allTeachers as $t) {
            $tBids   = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id');
            $tRev = 0;
            foreach ($tBids as $bid) {
                $fee = DB::table('batches')->where('id', $bid)->value('monthly_fee') ?? 0;
                $cnt = DB::table('students')->where('batch_id', $bid)->where('is_active', 1)->count();
                $tRev += $fee * $cnt * 12;
            }
            $tSal = ($t->monthly_salary ?? 0) * 12;
            $allRoi[$t->id] = $tSal > 0 ? $tRev / $tSal : 0;
        }
        arsort($allRoi);
        $roiRank       = (array_search($teacherId, array_keys($allRoi)) !== false) ? array_search($teacherId, array_keys($allRoi)) + 1 : count($allRoi);
        $totalTeachers = count($allRoi);

        // Owner decisions
        $decisions = [];
        if ($roi >= 5 && $classAvg >= 60) {
            $decisions[] = ['action' => 'Promote', 'color' => '#7fb685', 'reason' => "ROI {$roi}x + class avg {$classAvg}% — top performer", 'icon' => '↑'];
        }
        if ($roi >= 4 && $classAvg >= 55) {
            $decisions[] = ['action' => 'Raise', 'color' => '#a392c8', 'reason' => "Strong performance warrants salary review", 'icon' => '₹'];
        }
        if ($weakCount >= 10 || $classAvg < 45) {
            $decisions[] = ['action' => 'Training', 'color' => '#d4a574', 'reason' => "{$weakCount} weak topics — academic coaching needed", 'icon' => '◎'];
        }
        if ($atRiskPct > 40) {
            $decisions[] = ['action' => 'Cross-pair', 'color' => '#7a95c8', 'reason' => "{$atRiskPct}% at-risk — pair with higher-performing teacher", 'icon' => '⇄'];
        }
        if (empty($decisions)) {
            $decisions[] = ['action' => 'Monitor', 'color' => '#6a665f', 'reason' => 'Performance within acceptable range — continue monitoring', 'icon' => '◉'];
        }

        return view('owner.teacher-deep-dive', compact(
            'user', 'teacher', 'subjectName', 'batches',
            'classAvg', 'effectScore', 'roi', 'roiRank', 'totalTeachers',
            'studentIds', 'weakCount', 'strongCount', 'atRiskCount', 'atRiskPct',
            'strongTopics', 'weakTopics', 'batchRevenue',
            'annualRevenue', 'annualSalary', 'netContrib', 'tenureYears',
            'testTrend', 'decisions'
        ));
    }

    // ── Staff Decisions ──────────────────────────────────────────────────────
    public function staffDecisions()
    {
        [$user, $instituteId, $subjects] = $this->base();

        $teachers = DB::table('users')
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')
            ->where('is_active', 1)
            ->get();

        $decisions = [];
        foreach ($teachers as $t) {
            $batchIds   = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id');
            $subjectId  = $t->primary_subject_id;
            $nodeIds    = $subjectId ? DB::table('curriculum_nodes')->where('institute_id', $instituteId)->where('subject_id', $subjectId)->pluck('id') : collect();
            $studentIds = DB::table('students')->where('institute_id', $instituteId)->whereIn('batch_id', $batchIds)->where('is_active', 1)->pluck('id');

            $classAvg  = ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty()) ? (int) round(DB::table('student_subtopic_mastery')->whereIn('student_id', $studentIds)->whereIn('curriculum_node_id', $nodeIds)->avg('mastery_percentage') ?? 0) : 0;
            $weakCount = ($nodeIds->isNotEmpty() && $studentIds->isNotEmpty()) ? DB::table('student_subtopic_mastery')->whereIn('student_id', $studentIds)->whereIn('curriculum_node_id', $nodeIds)->groupBy('curriculum_node_id')->havingRaw('AVG(mastery_percentage) < 40')->pluck('curriculum_node_id')->count() : 0;
            $atRiskPct = 0;
            if ($nodeIds->isNotEmpty() && $studentIds->count() > 0) {
                $atRisk = DB::table('student_subtopic_mastery')->whereIn('student_id', $studentIds)->whereIn('curriculum_node_id', $nodeIds)->groupBy('student_id')->havingRaw('AVG(mastery_percentage) < 40')->pluck('student_id')->count();
                $atRiskPct = (int) round($atRisk / $studentIds->count() * 100);
            }

            $annualRevenue = 0;
            foreach ($batchIds as $bid) {
                $fee = DB::table('batches')->where('id', $bid)->value('monthly_fee') ?? 0;
                $cnt = DB::table('students')->where('batch_id', $bid)->where('is_active', 1)->count();
                $annualRevenue += $fee * $cnt * 12;
            }
            $annualSalary = ($t->monthly_salary ?? 0) * 12;
            $roi = $annualSalary > 0 ? round($annualRevenue / $annualSalary, 1) : 0;

            $pending = [];
            if ($roi >= 5 && $classAvg >= 60)  $pending[] = ['type' => 'promote',  'label' => 'Promotion Ready',      'color' => '#7fb685', 'reason' => "ROI {$roi}x · avg {$classAvg}%"];
            if ($roi >= 4 && $classAvg >= 55)  $pending[] = ['type' => 'raise',    'label' => 'Raise Recommended',    'color' => '#a392c8', 'reason' => "Strong performer, salary review due"];
            if ($weakCount >= 10 || $classAvg < 45) $pending[] = ['type' => 'training', 'label' => 'Training Needed',  'color' => '#d4a574', 'reason' => "{$weakCount} weak topics · avg {$classAvg}%"];
            if ($atRiskPct > 40)               $pending[] = ['type' => 'warning',  'label' => 'Performance Warning',  'color' => '#c87064', 'reason' => "{$atRiskPct}% students at risk"];

            if (!empty($pending)) {
                $decisions[] = ['teacher' => $t, 'classAvg' => $classAvg, 'roi' => $roi, 'students' => $studentIds->count(), 'pending' => $pending, 'subjectName' => $subjects->firstWhere('id', $subjectId)?->name ?? '—'];
            }
        }

        return view('owner.staff-decisions', compact('user', 'decisions'));
    }

    // ── Strategic Alerts ─────────────────────────────────────────────────────
    public function strategicAlerts()
    {
        [$user, $instituteId] = $this->base();
        [, $teachers, $monthlyRevenue, $monthlyCost] = $this->financialBase($instituteId);

        $annualProfit = ($monthlyRevenue - $monthlyCost) * 12;
        $margin = $monthlyRevenue > 0 ? (int) round(($monthlyRevenue - $monthlyCost) / $monthlyRevenue * 100) : 0;

        $totalStudents  = DB::table('students')->where('institute_id', $instituteId)->count();
        $activeStudents = DB::table('students')->where('institute_id', $instituteId)->where('is_active', 1)->count();
        $retentionRate  = $totalStudents > 0 ? (int) round($activeStudents / $totalStudents * 100) : 100;

        $classAvg = (int) round(DB::table('student_subtopic_mastery as m')->join('students as s', 's.id', '=', 'm.student_id')->where('s.institute_id', $instituteId)->avg('m.mastery_percentage') ?? 0);

        $subjects = DB::table('subjects')->where('institute_id', $instituteId)->where('is_active', 1)->get();

        $alerts = [];

        // Financial
        if ($margin < 20) {
            $alerts[] = ['level' => 'critical', 'category' => 'Financial', 'msg' => "Profit margin is {$margin}% — critically below 20% threshold. Immediate fee review or cost reduction required.", 'action' => route('owner.financial')];
        } elseif ($margin < 35) {
            $alerts[] = ['level' => 'warning', 'category' => 'Financial', 'msg' => "Profit margin at {$margin}% — target is 35%+. Consider fee revision next admission cycle.", 'action' => route('owner.financial')];
        } else {
            $alerts[] = ['level' => 'success', 'category' => 'Financial', 'msg' => "Profit margin at {$margin}% — healthy. Revenue exceeds cost targets.", 'action' => null];
        }

        // Retention
        if ($retentionRate < 85) {
            $alerts[] = ['level' => 'critical', 'category' => 'Retention', 'msg' => "Student retention at {$retentionRate}% — " . ($totalStudents - $activeStudents) . " dropouts detected. Immediate intervention required.", 'action' => route('owner.at-risk-students')];
        } elseif ($retentionRate < 95) {
            $alerts[] = ['level' => 'warning', 'category' => 'Retention', 'msg' => "Retention at {$retentionRate}% — " . ($totalStudents - $activeStudents) . " inactive students. Monitor closely.", 'action' => route('owner.at-risk-students')];
        }

        // Academic
        if ($classAvg < 40) {
            $alerts[] = ['level' => 'critical', 'category' => 'Academic', 'msg' => "Institute-wide class average is {$classAvg}% — critically low. Academic overhaul needed.", 'action' => route('owner.teachers')];
        } elseif ($classAvg < 55) {
            $alerts[] = ['level' => 'warning', 'category' => 'Academic', 'msg' => "Class average {$classAvg}% — below 55% target. Review teaching strategy.", 'action' => route('owner.teachers')];
        }

        // Per-teacher ROI
        foreach ($teachers as $t) {
            $batchIds = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id');
            $tRev = 0;
            foreach ($batchIds as $bid) {
                $fee = DB::table('batches')->where('id', $bid)->value('monthly_fee') ?? 0;
                $cnt = DB::table('students')->where('batch_id', $bid)->where('is_active', 1)->count();
                $tRev += $fee * $cnt * 12;
            }
            $tSal = ($t->monthly_salary ?? 0) * 12;
            $tRoi = $tSal > 0 ? round($tRev / $tSal, 1) : 0;
            if ($tSal > 0 && $tRoi < 2.0) {
                $alerts[] = ['level' => 'warning', 'category' => 'ROI', 'msg' => $t->name . " ROI is {$tRoi}x — minimum threshold is 2x. Review batch assignment or salary.", 'action' => route('owner.teacher-deep-dive', $t->id)];
            }
        }

        // Subject-level
        foreach ($subjects as $subj) {
            $nodeIds = DB::table('curriculum_nodes')->where('institute_id', $instituteId)->where('subject_id', $subj->id)->pluck('id');
            if ($nodeIds->isEmpty()) continue;
            $subAvg = (int) round(DB::table('student_subtopic_mastery')->whereIn('curriculum_node_id', $nodeIds)->avg('mastery_percentage') ?? 0);
            if ($subAvg < 40) {
                $alerts[] = ['level' => 'critical', 'category' => 'Subject', 'msg' => $subj->name . " average is {$subAvg}% — critically low. Teacher intervention required.", 'action' => route('owner.subject-roi')];
            }
        }

        // At-risk students
        $atRisk = DB::table('student_subtopic_mastery as m')->join('students as s', 's.id', '=', 'm.student_id')->where('s.institute_id', $instituteId)->where('s.is_active', 1)->groupBy('m.student_id')->havingRaw('AVG(m.mastery_percentage) < 40')->pluck('m.student_id')->count();
        if ($atRisk > 50) {
            $alerts[] = ['level' => 'critical', 'category' => 'Students', 'msg' => "{$atRisk} students below 40% mastery — significant dropout risk.", 'action' => route('owner.at-risk-students')];
        } elseif ($atRisk > 20) {
            $alerts[] = ['level' => 'warning', 'category' => 'Students', 'msg' => "{$atRisk} at-risk students identified. Remedial sessions required.", 'action' => route('owner.at-risk-students')];
        }

        // Sort: critical first
        usort($alerts, fn($a, $b) => ['critical' => 0, 'warning' => 1, 'success' => 2][$a['level']] - ['critical' => 0, 'warning' => 1, 'success' => 2][$b['level']]);

        return view('owner.strategic-alerts', compact('user', 'alerts', 'margin', 'retentionRate', 'classAvg', 'atRisk'));
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
        if ($search) $studentQuery->where(fn($q) => $q->where('s.name', 'like', "%$search%")->orWhere('s.roll_number', 'like', "%$search%"));

        $allStudents = $studentQuery->select('s.id', 's.name', 's.roll_number', 'b.name as batch_name', 's.batch_id')->get();

        $masteryMap = DB::table('student_subtopic_mastery')
            ->whereIn('student_id', $allStudents->pluck('id'))
            ->whereIn('curriculum_node_id', $allNodeIds)
            ->groupBy('student_id')
            ->selectRaw('student_id, AVG(mastery_percentage) as avg_m, COUNT(DISTINCT curriculum_node_id) as topics')
            ->get()->keyBy('student_id');

        $atRisk = $allStudents->map(function ($s) use ($masteryMap) {
            $m = $masteryMap->get($s->id);
            $s->avg_m  = $m ? (int) round($m->avg_m) : 0;
            $s->topics = $m ? $m->topics : 0;
            return $s;
        })->filter(fn($s) => $s->avg_m < 40 && $s->topics > 0)->sortBy('avg_m')->values();

        return view('owner.at-risk-students', compact('user', 'subjects', 'batches', 'atRisk', 'batchFilter', 'subjectFilter', 'search'));
    }

    // ── Notifications ────────────────────────────────────────────────────────
    public function notifications()
    {
        [$user, $instituteId] = $this->base();
        $notifications = DB::table('notifications')->where('institute_id', $instituteId)->where('user_id', $user->id)->orderByDesc('created_at')->limit(30)->get();
        return view('owner.notifications', compact('user', 'notifications'));
    }

    // ── Help ─────────────────────────────────────────────────────────────────
    public function help()
    {
        [$user] = $this->base();
        return view('owner.help', compact('user'));
    }
}
