@extends('layouts.owner')
@section('title', 'Notifications')
@section('breadcrumb', 'Notifications')

@section('content')
<div style="max-width:800px;">
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Notifications</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">System and strategic notifications</p>
    </div>

    @forelse($notifications as $n)
    <div style="background:#14141b;border:1px solid rgba(245,241,232,{{ $n->is_read ? '0.06' : '0.12' }});border-radius:8px;padding:16px 20px;margin-bottom:8px;{{ $n->is_read ? '' : 'border-left:2px solid #a392c8;' }}">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
            <div style="flex:1;">
                <p style="font-size:13px;font-weight:{{ $n->is_read ? '500' : '600' }};color:{{ $n->is_read ? '#a8a39c' : '#f5f1e8' }};margin:0 0 4px;">{{ $n->title }}</p>
                <p style="font-size:12px;color:#6a665f;margin:0;line-height:1.5;">{{ $n->message }}</p>
            </div>
            <p style="font-size:10px;color:#4a4740;white-space:nowrap;margin:0;">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</p>
        </div>
    </div>
    @empty
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:48px;text-align:center;">
        <p style="font-size:14px;color:#6a665f;margin:0;">No notifications yet.</p>
    </div>
    @endforelse
</div>
@endsection
