<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $instituteId = $user->institute_id;
        $teacherId   = $user->id;

        // Teacher's batches and subject
        $batchIds = DB::table('user_batch_assignments')
            ->where('user_id', $teacherId)
            ->pluck('batch_id');

        $subjectId = $user->primary_subject_id;

        $subject = $subjectId
            ? DB::table('subjects')->where('id', $subjectId)->first()
            : null;

        // Total students across teacher's batches
        $studentCount = DB::table('students')
            ->where('institute_id', $instituteId)
            ->whereIn('batch_id', $batchIds)
            ->where('is_active', 1)
            ->count();

        // Tests conducted for this teacher's subject + batches
        $testsCount = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->count();

        // Class average mastery for this subject
        $avgMastery = 0;
        if ($subjectId) {
            $nodeIds = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subjectId)
                ->pluck('id');

            if ($nodeIds->isNotEmpty()) {
                $avgMastery = (int) round(
                    DB::table('student_subtopic_mastery')
                        ->whereIn('student_id', function ($q) use ($instituteId, $batchIds) {
                            $q->select('id')->from('students')
                              ->where('institute_id', $instituteId)
                              ->whereIn('batch_id', $batchIds);
                        })
                        ->whereIn('curriculum_node_id', $nodeIds)
                        ->avg('mastery_percentage') ?? 0
                );
            }
        }

        // At-risk students (mastery < 40% on average)
        $atRiskCount = 0;
        if ($subjectId) {
            $nodeIds = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subjectId)
                ->pluck('id');

            if ($nodeIds->isNotEmpty()) {
                $studentIds = DB::table('students')
                    ->where('institute_id', $instituteId)
                    ->whereIn('batch_id', $batchIds)
                    ->where('is_active', 1)
                    ->pluck('id');

                $atRiskCount = DB::table('student_subtopic_mastery')
                    ->whereIn('student_id', $studentIds)
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->groupBy('student_id')
                    ->havingRaw('AVG(mastery_percentage) < 40')
                    ->pluck('student_id')->count();
            }
        }

        // Subject-level stats for the banner
        $weakTopicsCount   = 0;
        $strongTopicsCount = 0;
        if ($subjectId) {
            $nodeIds = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subjectId)
                ->pluck('id');

            if ($nodeIds->isNotEmpty()) {
                $studentIds = DB::table('students')
                    ->where('institute_id', $instituteId)
                    ->whereIn('batch_id', $batchIds)
                    ->where('is_active', 1)
                    ->pluck('id');

                $topicAvgs = DB::table('student_subtopic_mastery')
                    ->whereIn('student_id', $studentIds)
                    ->whereIn('curriculum_node_id', $nodeIds)
                    ->groupBy('curriculum_node_id')
                    ->selectRaw('curriculum_node_id, AVG(mastery_percentage) as avg_m')
                    ->get();

                $weakTopicsCount   = $topicAvgs->where('avg_m', '<', 40)->count();
                $strongTopicsCount = $topicAvgs->where('avg_m', '>=', 70)->count();
            }
        }

        // Recent tests (last 5 analyzed)
        $recentTests = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->where('status', 'analyzed')
            ->orderByDesc('test_date')
            ->limit(5)
            ->get();

        // For each test, compute avg marks
        foreach ($recentTests as $t) {
            $t->avg_marks = (int) round(
                DB::table('test_results_cache')
                    ->where('test_id', $t->id)
                    ->whereIn('student_id', function ($q) use ($instituteId, $batchIds) {
                        $q->select('id')->from('students')
                          ->where('institute_id', $instituteId)
                          ->whereIn('batch_id', $batchIds);
                    })
                    ->avg('total_marks') ?? 0
            );
            $t->max_marks = $t->total_questions * 4;
        }

        // Weak topics (lowest mastery nodes for this subject)
        $weakTopics = collect();
        if ($subjectId) {
            $nodeIds = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $subjectId)
                ->pluck('id');

            if ($nodeIds->isNotEmpty()) {
                $studentIds = DB::table('students')
                    ->where('institute_id', $instituteId)
                    ->whereIn('batch_id', $batchIds)
                    ->where('is_active', 1)
                    ->pluck('id');

                $weakTopics = DB::table('student_subtopic_mastery as m')
                    ->join('curriculum_nodes as cn', 'cn.id', '=', 'm.curriculum_node_id')
                    ->whereIn('m.student_id', $studentIds)
                    ->whereIn('m.curriculum_node_id', $nodeIds)
                    ->groupBy('m.curriculum_node_id', 'cn.name', 'cn.code')
                    ->selectRaw('cn.name as topic_name, cn.code as topic_code, AVG(m.mastery_percentage) as avg_mastery')
                    ->orderByRaw('AVG(m.mastery_percentage) ASC')
                    ->limit(5)
                    ->get();
            }
        }

        return view('teacher.dashboard', compact(
            'user', 'subject', 'studentCount', 'testsCount',
            'avgMastery', 'atRiskCount',
            'weakTopicsCount', 'strongTopicsCount',
            'recentTests', 'weakTopics'
        ));
    }
}
