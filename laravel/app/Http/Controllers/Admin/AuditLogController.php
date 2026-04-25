<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    private function instituteId(): int
    {
        return Auth::user()->institute_id;
    }

    public function index(Request $request)
    {
        $instituteId = $this->instituteId();

        $query = DB::table('audit_logs')
            ->where('audit_logs.institute_id', $instituteId)
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->select(
                'audit_logs.*',
                'users.name as user_name',
                'users.role as user_role'
            )
            ->orderBy('audit_logs.created_at', 'desc');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('audit_logs.description', 'like', $s)
                  ->orWhere('audit_logs.entity_type', 'like', $s)
                  ->orWhere('users.name', 'like', $s);
            });
        }

        if ($request->filled('user_id')) {
            $query->where('audit_logs.user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('audit_logs.action', 'like', $request->action . '%');
        }

        if ($request->filled('period')) {
            $query->where('audit_logs.created_at', '>=', match($request->period) {
                'today'   => now()->startOfDay(),
                'week'    => now()->subWeek(),
                'month'   => now()->subMonth(),
                default   => now()->subYear(),
            });
        }

        $logs = $query->paginate(50)->withQueryString();

        $stats = [
            'total'   => DB::table('audit_logs')->where('institute_id', $instituteId)->count(),
            'today'   => DB::table('audit_logs')->where('institute_id', $instituteId)->whereDate('created_at', today())->count(),
            'errors'  => DB::table('audit_logs')->where('institute_id', $instituteId)->where('created_at', '>=', now()->subHour())->where('action', 'like', '%.error%')->count(),
        ];

        $users = DB::table('users')->where('institute_id', $instituteId)->select('id', 'name')->orderBy('name')->get();

        return view('admin.audit-log.index', compact('logs', 'stats', 'users'));
    }
}
