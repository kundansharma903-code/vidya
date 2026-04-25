<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $tab = in_array($request->tab, ['all', 'unread', 'errors', 'system', 'success']) ? $request->tab : 'all';

        $query = DB::table('notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        match ($tab) {
            'unread'  => $query->where('is_read', false),
            'errors'  => $query->where('type', 'like', '%error%')->orWhere('type', 'like', '%alert%'),
            'system'  => $query->where('type', 'like', 'system%'),
            'success' => $query->where('type', 'like', '%success%')->orWhere('type', 'like', '%ready%'),
            default   => null,
        };

        $notifications = $query->paginate(30)->withQueryString();

        $counts = [
            'all'     => DB::table('notifications')->where('user_id', $userId)->count(),
            'unread'  => DB::table('notifications')->where('user_id', $userId)->where('is_read', false)->count(),
            'errors'  => DB::table('notifications')->where('user_id', $userId)->where(function ($q) { $q->where('type', 'like', '%error%')->orWhere('type', 'like', '%alert%'); })->count(),
            'system'  => DB::table('notifications')->where('user_id', $userId)->where('type', 'like', 'system%')->count(),
            'success' => DB::table('notifications')->where('user_id', $userId)->where(function ($q) { $q->where('type', 'like', '%success%')->orWhere('type', 'like', '%ready%'); })->count(),
        ];

        return view('admin.notifications.index', compact('notifications', 'counts', 'tab'));
    }

    public function markRead(int $id)
    {
        DB::table('notifications')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['is_read' => true, 'read_at' => now(), 'updated_at' => now()]);

        return back();
    }

    public function markAllRead()
    {
        DB::table('notifications')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now(), 'updated_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
