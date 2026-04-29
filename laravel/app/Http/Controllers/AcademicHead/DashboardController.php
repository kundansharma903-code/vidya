<?php

namespace App\Http\Controllers\AcademicHead;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $instituteId = $user->institute_id;

        $subjects = DB::table('subjects')
            ->where('institute_id', $instituteId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        // ── Curriculum Coverage ─────────────────────────────────────────
        $totalNodes   = DB::table('curriculum_nodes')->where('institute_id', $instituteId)->count();
        $coveredNodes = DB::table('curriculum_nodes as cn')
            ->where('cn.institute_id', $instituteId)
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('student_subtopic_mastery as m')
                  ->whereColumn('m.curriculum_node_id', 'cn.id');
            })
            ->count();
        $curriculumCoverage = $totalNodes > 0 ? (int) round($coveredNodes / $totalNodes * 100) : 0;

        // ── Test Quality Score ──────────────────────────────────────────
        // Based on avg distinct topic codes per test
        $analyzedTests = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->pluck('id');

        $testQuality = 0;
        if ($analyzedTests->isNotEmpty()) {
            $avgTopics = DB::table('student_subtopic_mastery as m')
                ->join('curriculum_nodes as cn', 'cn.id', '=', 'm.curriculum_node_id')
                ->groupBy('m.student_id')
                ->selectRaw('COUNT(DISTINCT m.curriculum_node_id) as topic_count')
                ->get()
                ->avg('topic_count') ?? 0;
            // Target 45 topics = 100%, scale accordingly
            $testQuality = (int) min(100, round($avgTopics / 45 * 100));
        }

        // ── Teacher Effectiveness ───────────────────────────────────────
        $teacherEffectiveness = 0;
        $teachers = DB::table('users')
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')
            ->get();

        if ($teachers->isNotEmpty()) {
            $scores = [];
            foreach ($teachers as $t) {
                $batchIds = DB::table('user_batch_assignments')
                    ->where('user_id', $t->id)->pluck('batch_id');
                $subjectId = $t->primary_subject_id;
                if (!$subjectId || $batchIds->isEmpty()) continue;

                $nodeIds = DB::table('curriculum_nodes')
                    ->where('institute_id', $instituteId)
                    ->where('subject_id', $subjectId)
                    ->pluck('id');
                if ($nodeIds->isEmpty()) continue;

                $studentIds = DB::table('students')
                    ->where('institute_id', $instituteId)
                    ->whereIn('batch_id', $batchIds)
                    ->where('is_active', 1)
                    ->pluck('id');
                if ($studentIds->isEmpty()) continue;

                $avg = DB::table('student_subtopic_mastery')
                    ->whereIn('student_id', $studentIds)
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->avg('mastery_percentage') ?? 0;
                $scores[] = $avg;
            }
            $teacherEffectiveness = $scores ? (int) round(array_sum($scores) / count($scores)) : 0;
        }

        // ── Student Retention ───────────────────────────────────────────
        $totalStudents  = DB::table('students')->where('institute_id', $instituteId)->count();
        $activeStudents = DB::table('students')->where('institute_id', $instituteId)->where('is_active', 1)->count();
        $studentRetention = $totalStudents > 0 ? (int) round($activeStudents / $totalStudents * 100) : 100;

        // ── Health Score ────────────────────────────────────────────────
        $healthScore = (int) round(
            $curriculumCoverage * 0.25 +
            $testQuality        * 0.25 +
            $teacherEffectiveness * 0.25 +
            $studentRetention   * 0.25
        );

        // ── 4 KPIs ──────────────────────────────────────────────────────
        $now = now();
        $testsThisMonth = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->whereYear('test_date', $now->year)
            ->whereMonth('test_date', $now->month)
            ->count();

        $classAvg = (int) round(
            DB::table('student_subtopic_mastery')->avg('mastery_percentage') ?? 0
        );

        $allNodeIds = DB::table('curriculum_nodes')->where('institute_id', $instituteId)->pluck('id');
        $weakTopicsCount = 0;
        if ($allNodeIds->isNotEmpty()) {
            $weakTopicsCount = DB::table('student_subtopic_mastery')
                ->whereIn('curriculum_node_id', $allNodeIds)
                ->groupBy('curriculum_node_id')
                ->havingRaw('AVG(mastery_percentage) < 40')
                ->pluck('curriculum_node_id')->count();
        }

        $atRiskCount = 0;
        if ($allNodeIds->isNotEmpty()) {
            $atRiskCount = DB::table('student_subtopic_mastery')
                ->whereIn('curriculum_node_id', $allNodeIds)
                ->groupBy('student_id')
                ->havingRaw('AVG(mastery_percentage) < 40')
                ->pluck('student_id')->count();
        }

        // ── Subject performance ─────────────────────────────────────────
        $subjectPerformance = [];
        foreach ($subjects as $subj) {
            $nodeIds = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subj->id)
                ->pluck('id');

            $avg = $nodeIds->isNotEmpty()
                ? (int) round(DB::table('student_subtopic_mastery')
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->avg('mastery_percentage') ?? 0)
                : 0;

            $covered = $nodeIds->isNotEmpty()
                ? DB::table('student_subtopic_mastery')
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->distinct('curriculum_node_id')
                    ->count('curriculum_node_id')
                : 0;

            $subjectPerformance[] = [
                'id'       => $subj->id,
                'name'     => $subj->name,
                'code'     => $subj->code,
                'avg'      => $avg,
                'total'    => $nodeIds->count(),
                'covered'  => $covered,
                'pct'      => $nodeIds->count() > 0 ? (int) round($covered / $nodeIds->count() * 100) : 0,
            ];
        }

        // ── Coverage bars (per-subject) ─────────────────────────────────
        // Already in subjectPerformance above

        // ── Academic Alerts ─────────────────────────────────────────────
        $alerts = [];
        foreach ($subjectPerformance as $sp) {
            if ($sp['avg'] < 40 && $sp['total'] > 0) {
                $alerts[] = ['type' => 'danger', 'msg' => $sp['name'] . ' class average critically low (' . $sp['avg'] . '%)'];
            } elseif ($sp['pct'] < 50 && $sp['total'] > 0) {
                $alerts[] = ['type' => 'warning', 'msg' => $sp['name'] . ' curriculum only ' . $sp['pct'] . '% covered'];
            }
        }
        if ($atRiskCount > 30) {
            $alerts[] = ['type' => 'danger', 'msg' => $atRiskCount . ' students at risk (mastery < 40%)'];
        }
        if ($testsThisMonth === 0) {
            $alerts[] = ['type' => 'warning', 'msg' => 'No tests conducted this month'];
        }
        if (empty($alerts)) {
            $alerts[] = ['type' => 'success', 'msg' => 'All subjects on track — no critical issues'];
        }

        return view('academic-head.dashboard', compact(
            'user', 'subjects',
            'healthScore', 'curriculumCoverage', 'testQuality', 'teacherEffectiveness', 'studentRetention',
            'testsThisMonth', 'classAvg', 'weakTopicsCount', 'atRiskCount',
            'subjectPerformance', 'alerts'
        ));
    }
}
