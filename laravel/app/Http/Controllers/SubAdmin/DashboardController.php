<?php

namespace App\Http\Controllers\SubAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $instituteId = Auth::user()->institute_id;
        $today       = now()->toDateString();
        $monthStart  = now()->startOfMonth()->toDateString();

        // KPIs
        $uploadsToday = DB::table('omr_upload_batches')
            ->where('institute_id', $instituteId)
            ->whereDate('created_at', $today)
            ->count();

        $pendingTests = DB::table('tests')
            ->where('institute_id', $instituteId)
            ->whereIn('status', ['scheduled', 'conducted'])
            ->count();

        $processedThisMonth = DB::table('omr_upload_batches')
            ->where('institute_id', $instituteId)
            ->where('status', 'completed')
            ->whereDate('created_at', '>=', $monthStart)
            ->count();

        $totalUploads     = DB::table('omr_upload_batches')->where('institute_id', $instituteId)->count();
        $unmatchedUploads = DB::table('omr_upload_batches')
            ->where('institute_id', $instituteId)
            ->where('unmatched_rows', '>', 0)
            ->count();
        $errorRate = $totalUploads > 0 ? round(($unmatchedUploads / $totalUploads) * 100) : 0;

        // Processing count for subtitle
        $processingCount = DB::table('omr_upload_batches')
            ->where('institute_id', $instituteId)
            ->whereIn('status', ['uploaded', 'validating', 'matching'])
            ->count();

        // Tests ready for upload (scheduled/conducted, no completed upload yet)
        $pendingTestRows = DB::table('tests')
            ->where('tests.institute_id', $instituteId)
            ->whereIn('tests.status', ['scheduled', 'conducted'])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('omr_upload_batches')
                    ->whereColumn('omr_upload_batches.test_id', 'tests.id')
                    ->where('omr_upload_batches.status', 'completed');
            })
            ->orderBy('tests.test_date', 'desc')
            ->limit(5)
            ->get(['tests.id', 'tests.test_code', 'tests.name', 'tests.test_date', 'tests.status']);

        // Attach student counts via test_batches → students
        $pendingTestRows = $pendingTestRows->map(function ($test) use ($instituteId) {
            $batchIds = DB::table('test_batches')->where('test_id', $test->id)->pluck('batch_id');
            $studentCount = $batchIds->isNotEmpty()
                ? DB::table('user_batch_assignments')
                    ->whereIn('batch_id', $batchIds)
                    ->distinct('user_id')
                    ->count('user_id')
                : 0;
            $test->student_count = $studentCount;
            return $test;
        });

        // Recent uploads
        $recentUploads = DB::table('omr_upload_batches')
            ->where('omr_upload_batches.institute_id', $instituteId)
            ->leftJoin('tests', 'omr_upload_batches.test_id', '=', 'tests.id')
            ->orderBy('omr_upload_batches.created_at', 'desc')
            ->limit(4)
            ->get([
                'omr_upload_batches.id',
                'omr_upload_batches.status as upload_status',
                'omr_upload_batches.created_at',
                'omr_upload_batches.unmatched_rows',
                'tests.test_code',
                'tests.name as test_name',
            ]);

        $firstName = explode(' ', Auth::user()->name)[0];
        $hour      = (int) now()->format('H');
        $greeting  = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default    => 'Good evening',
        };

        return view('sub-admin.dashboard', compact(
            'uploadsToday', 'pendingTests', 'processedThisMonth', 'errorRate',
            'processingCount', 'pendingTestRows', 'recentUploads',
            'firstName', 'greeting'
        ));
    }
}
