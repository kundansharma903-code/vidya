<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $instituteId = $user->institute_id;

        // ── Teachers ──────────────────────────────────────────────────────
        $teachers = DB::table('users')
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')
            ->where('is_active', 1)
            ->get();

        // ── Revenue calculation ───────────────────────────────────────────
        // Sum: for each active batch, monthly_fee × student count × 12
        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->where('is_active', 1)
            ->get();

        $monthlyRevenue = 0;
        foreach ($batches as $b) {
            $count = DB::table('students')
                ->where('batch_id', $b->id)
                ->where('is_active', 1)
                ->count();
            $monthlyRevenue += ($b->monthly_fee ?? 0) * $count;
        }
        $annualRevenue = $monthlyRevenue * 12;

        // ── Cost calculation ──────────────────────────────────────────────
        $allStaff = DB::table('users')
            ->where('institute_id', $instituteId)
            ->whereIn('role', ['teacher', 'sub_admin', 'typist', 'academic_head'])
            ->where('is_active', 1)
            ->get();

        $monthlyCost  = $allStaff->sum('monthly_salary');
        $annualCost   = $monthlyCost * 12;

        $annualProfit = $annualRevenue - $annualCost;
        $margin       = $annualRevenue > 0 ? (int) round($annualProfit / $annualRevenue * 100) : 0;

        // ── Student KPIs ──────────────────────────────────────────────────
        $totalStudents  = DB::table('students')->where('institute_id', $instituteId)->count();
        $activeStudents = DB::table('students')->where('institute_id', $instituteId)->where('is_active', 1)->count();
        $retentionRate  = $totalStudents > 0 ? (int) round($activeStudents / $totalStudents * 100) : 100;

        // ── Academic KPIs ─────────────────────────────────────────────────
        $totalNodes   = DB::table('curriculum_nodes')->where('institute_id', $instituteId)->count();
        $coveredNodes = DB::table('curriculum_nodes as cn')
            ->where('cn.institute_id', $instituteId)
            ->whereExists(fn($q) => $q->select(DB::raw(1))->from('student_subtopic_mastery as m')->whereColumn('m.curriculum_node_id', 'cn.id'))
            ->count();
        $curriculumPct = $totalNodes > 0 ? (int) round($coveredNodes / $totalNodes * 100) : 0;

        $classAvg = (int) round(
            DB::table('student_subtopic_mastery as m')
                ->join('students as s', 's.id', '=', 'm.student_id')
                ->where('s.institute_id', $instituteId)
                ->avg('m.mastery_percentage') ?? 0
        );

        // ── Per-teacher ROI ───────────────────────────────────────────────
        $teacherRoi = [];
        foreach ($teachers as $t) {
            $batchIds   = DB::table('user_batch_assignments')->where('user_id', $t->id)->pluck('batch_id');
            $studentCnt = DB::table('students')->where('institute_id', $instituteId)->whereIn('batch_id', $batchIds)->where('is_active', 1)->count();

            $tRevenue = 0;
            foreach ($batchIds as $bid) {
                $fee = DB::table('batches')->where('id', $bid)->value('monthly_fee') ?? 0;
                $cnt = DB::table('students')->where('batch_id', $bid)->where('is_active', 1)->count();
                $tRevenue += $fee * $cnt * 12;
            }
            $tSalary = ($t->monthly_salary ?? 0) * 12;
            $tRoi    = $tSalary > 0 ? round($tRevenue / $tSalary, 1) : 0;

            $teacherRoi[] = ['teacher' => $t, 'revenue' => $tRevenue, 'salary' => $tSalary, 'roi' => $tRoi, 'students' => $studentCnt];
        }

        // ── Business Health Score ─────────────────────────────────────────
        $financeHealth  = min(100, max(0, 50 + $margin));  // margin 0%=50, 50%=100
        $academicHealth = $classAvg;
        $retentionHealth = $retentionRate;
        $coverageHealth = $curriculumPct;
        $healthScore = (int) round(
            $financeHealth   * 0.30 +
            $academicHealth  * 0.25 +
            $retentionHealth * 0.25 +
            $coverageHealth  * 0.20
        );

        // ── Strategic Alerts ─────────────────────────────────────────────
        $alerts = $this->buildAlerts($instituteId, $margin, $retentionRate, $classAvg, $teachers, $teacherRoi);

        // ── Recent tests ──────────────────────────────────────────────────
        $recentTests = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->orderByDesc('test_date')
            ->limit(5)
            ->get();

        return view('owner.dashboard', compact(
            'user',
            'monthlyRevenue', 'annualRevenue',
            'monthlyCost', 'annualCost',
            'annualProfit', 'margin',
            'totalStudents', 'activeStudents', 'retentionRate',
            'curriculumPct', 'classAvg',
            'healthScore', 'financeHealth', 'academicHealth',
            'teacherRoi', 'alerts', 'recentTests'
        ));
    }

    private function buildAlerts(int $instituteId, int $margin, int $retention, int $classAvg, $teachers, array $teacherRoi): array
    {
        $alerts = [];

        if ($margin < 20) {
            $alerts[] = ['level' => 'critical', 'category' => 'Financial', 'msg' => "Profit margin is {$margin}% — below 20% threshold. Review fee structure or reduce costs."];
        } elseif ($margin < 35) {
            $alerts[] = ['level' => 'warning', 'category' => 'Financial', 'msg' => "Profit margin at {$margin}% — target is 35%+. Consider fee revision next cycle."];
        }

        if ($retention < 85) {
            $alerts[] = ['level' => 'critical', 'category' => 'Retention', 'msg' => "Student retention at {$retention}% — {" . (100 - $retention) . "}% dropout rate needs intervention."];
        }

        if ($classAvg < 40) {
            $alerts[] = ['level' => 'critical', 'category' => 'Academic', 'msg' => "Institute-wide class average is {$classAvg}% — critically low. Academic review required."];
        }

        foreach ($teacherRoi as $tr) {
            if ($tr['roi'] > 0 && $tr['roi'] < 2.0) {
                $alerts[] = ['level' => 'warning', 'category' => 'ROI', 'msg' => $tr['teacher']->name . " ROI is {$tr['roi']}x — below 2x minimum. Review batch assignment."];
            }
        }

        $atRisk = DB::table('student_subtopic_mastery as m')
            ->join('students as s', 's.id', '=', 'm.student_id')
            ->where('s.institute_id', $instituteId)
            ->where('s.is_active', 1)
            ->groupBy('m.student_id')
            ->havingRaw('AVG(m.mastery_percentage) < 40')
            ->count(DB::raw('DISTINCT m.student_id'));

        if ($atRisk > 50) {
            $alerts[] = ['level' => 'critical', 'category' => 'Students', 'msg' => "{$atRisk} students below 40% mastery — high dropout risk."];
        } elseif ($atRisk > 20) {
            $alerts[] = ['level' => 'warning', 'category' => 'Students', 'msg' => "{$atRisk} at-risk students identified. Remedial sessions needed."];
        }

        if (empty($alerts)) {
            $alerts[] = ['level' => 'success', 'category' => 'All Clear', 'msg' => 'No critical alerts. Business and academics are on track.'];
        }

        return $alerts;
    }
}
