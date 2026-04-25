@extends('layouts.teacher')
@section('title', 'Notifications')
@section('breadcrumb', 'Notifications')

@section('content')
<div style="max-width:720px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Notifications</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">System announcements and updates</p>
    </div>

    @if ($notifications->isEmpty())
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:64px;text-align:center;">
            <p style="font-size:28px;margin:0 0 12px;">🔔</p>
            <p style="font-size:14px;color:#a8a39c;margin:0 0 6px;">You're all caught up!</p>
            <p style="font-size:12px;color:#6a665f;margin:0;">No notifications right now.</p>
        </div>
    @else
        <div style="display:flex;flex-direction:column;gap:8px;">
            @foreach ($notifications as $n)
                <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:14px 18px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                        <p style="font-size:13px;color:#f5f1e8;margin:0 0 4px;line-height:1.5;">{{ $n->message ?? $n->title ?? 'Notification' }}</p>
                        <span style="font-size:10px;color:#6a665f;flex-shrink:0;">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
