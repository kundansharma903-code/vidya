<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $instituteId = Auth::user()->institute_id;

        $stats = [
            'total_courses'  => DB::table('courses')->where('institute_id', $instituteId)->count(),
            'active_batches' => DB::table('batches')->where('institute_id', $instituteId)->where('is_active', true)->count(),
            'total_students' => DB::table('students')->where('institute_id', $instituteId)->count(),
            'active_staff'   => DB::table('users')
                                    ->where('institute_id', $instituteId)
                                    ->whereIn('role', ['teacher', 'reception', 'sub_admin', 'academic_head'])
                                    ->where('is_active', true)
                                    ->count(),
        ];

        $recentActivity = DB::table('audit_logs')
            ->where('institute_id', $instituteId)
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $hour = (int) now()->format('H');
        $greeting = match(true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default    => 'Good evening',
        };

        return view('admin.dashboard', compact('stats', 'recentActivity', 'greeting'));
    }
}
