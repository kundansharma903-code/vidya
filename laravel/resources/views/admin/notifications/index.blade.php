@extends('layouts.admin')

@section('title', 'Notifications')
@section('breadcrumb', 'Notifications')

@section('content')

{{-- Header --}}
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 style="font-size:24px;font-weight:700;color:#f5f1e8;letter-spacing:-0.48px;margin:0 0 4px 0;">Notifications</h1>
        <p style="font-size:14px;color:#a8a39c;margin:0;">Your alerts, system messages, and activity updates.</p>
    </div>
    @if ($counts['unread'] > 0)
        <form method="POST" action="{{ route('admin.notifications.read-all') }}">
            @csrf
            <button type="submit" style="background:#14141b;border:1px solid rgba(122,149,200,0.25);border-radius:6px;padding:9px 16px;font-size:13px;font-weight:500;color:#7a95c8;cursor:pointer;">
                Mark All as Read
            </button>
        </form>
    @endif
</div>

@if (session('success'))
    <div style="background:rgba(127,182,133,0.12);border:1px solid rgba(127,182,133,0.25);border-radius:8px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#7fb685" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span style="font-size:13px;color:#7fb685;">{{ session('success') }}</span>
    </div>
@endif

{{-- Tabs --}}
@php
    $tabDefs = [
        'all'     => ['label' => 'All',     'count' => $counts['all']],
        'unread'  => ['label' => 'Unread',  'count' => $counts['unread']],
        'errors'  => ['label' => 'Errors',  'count' => $counts['errors']],
        'system'  => ['label' => 'System',  'count' => $counts['system']],
        'success' => ['label' => 'Success', 'count' => $counts['success']],
    ];
@endphp
<div style="display:flex;gap:0;border-bottom:1px solid rgba(245,241,232,0.08);margin-bottom:24px;">
    @foreach ($tabDefs as $key => $td)
    <a href="{{ route('admin.notifications', ['tab' => $key]) }}"
       style="display:flex;align-items:center;gap:6px;padding:10px 18px;font-size:13px;font-weight:500;text-decoration:none;border-bottom:2px solid {{ $tab === $key ? '#7a95c8' : 'transparent' }};color:{{ $tab === $key ? '#f5f1e8' : '#a8a39c' }};background:{{ $tab === $key ? 'rgba(122,149,200,0.08)' : 'transparent' }};margin-bottom:-1px;transition:color 0.15s;">
        {{ $td['label'] }}
        @if ($td['count'] > 0)
            <span style="font-size:10px;font-weight:600;background:{{ $tab === $key ? '#7a95c8' : 'rgba(245,241,232,0.08)' }};color:{{ $tab === $key ? '#08080a' : '#6a665f' }};border-radius:9px;padding:1px 6px;min-width:18px;text-align:center;">{{ $td['count'] }}</span>
        @endif
    </a>
    @endforeach
</div>

{{-- Notification cards --}}
@if ($notifications->isEmpty())
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:64px 32px;text-align:center;">
        <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="#6a665f" stroke-width="1.25" style="margin:0 auto 12px auto;display:block;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <p style="font-size:14px;font-weight:500;color:#a8a39c;margin:0 0 4px 0;">All clear</p>
        <p style="font-size:13px;color:#6a665f;margin:0;">No notifications in this category.</p>
    </div>
@else
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach ($notifications as $n)
        @php
            $typePrefix = explode('_', $n->type)[0] ?? '';
            $isError = str_contains($n->type, 'error') || str_contains($n->type, 'alert');
            $isSuccess = str_contains($n->type, 'success') || str_contains($n->type, 'ready');
            $isWarning = str_contains($n->type, 'warning') || str_contains($n->type, 'risk');
            $isSystem = str_starts_with($n->type, 'system');

            if ($isError)        { $borderColor = '#e05252'; $iconBg = 'rgba(224,82,82,0.12)'; $iconColor = '#e05252'; $icon = '!'; }
            elseif ($isSuccess)  { $borderColor = '#7fb685'; $iconBg = 'rgba(127,182,133,0.12)'; $iconColor = '#7fb685'; $icon = '✓'; }
            elseif ($isWarning)  { $borderColor = '#e0a352'; $iconBg = 'rgba(224,163,82,0.12)'; $iconColor = '#e0a352'; $icon = '⚠'; }
            elseif ($isSystem)   { $borderColor = '#a8a39c'; $iconBg = 'rgba(168,163,156,0.10)'; $iconColor = '#a8a39c'; $icon = '⚙'; }
            else                 { $borderColor = '#7a95c8'; $iconBg = 'rgba(122,149,200,0.12)'; $iconColor = '#7a95c8'; $icon = 'i'; }

            $cardBg = $n->is_read ? '#14141b' : '#1a1a24';

            $data = $n->data ? json_decode($n->data, true) : [];
            $actionUrl = $data['action_url'] ?? null;
        @endphp
        <div style="background:{{ $cardBg }};border:1px solid rgba(245,241,232,0.08);border-left:3px solid {{ $borderColor }};border-radius:8px;padding:14px 16px;display:flex;align-items:flex-start;gap:14px;position:relative;">

            {{-- Type icon --}}
            <div style="width:34px;height:34px;background:{{ $iconBg }};border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;font-weight:700;color:{{ $iconColor }};">
                {{ $icon }}
            </div>

            {{-- Content --}}
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                    <span style="font-size:13px;font-weight:{{ $n->is_read ? '400' : '600' }};color:#f5f1e8;">{{ $n->title }}</span>
                    @if (!$n->is_read)
                        <span style="width:6px;height:6px;background:#7a95c8;border-radius:50%;flex-shrink:0;display:inline-block;"></span>
                    @endif
                    <span style="font-size:10px;font-weight:600;background:rgba(245,241,232,0.05);color:#6a665f;border-radius:3px;padding:1px 6px;text-transform:uppercase;letter-spacing:0.4px;flex-shrink:0;">
                        {{ str_replace('_', ' ', $n->type) }}
                    </span>
                </div>
                <p style="font-size:13px;color:#a8a39c;margin:0 0 8px 0;line-height:1.5;">{{ $n->message }}</p>
                <div style="display:flex;align-items:center;gap:12px;">
                    <span style="font-size:11px;color:#6a665f;">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</span>
                    @if ($actionUrl)
                        <a href="{{ $actionUrl }}" style="font-size:11px;color:#7a95c8;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">View →</a>
                    @endif
                </div>
            </div>

            {{-- Mark as read --}}
            @if (!$n->is_read)
                <form method="POST" action="{{ route('admin.notifications.read', $n->id) }}" style="flex-shrink:0;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" title="Mark as read"
                            style="background:none;border:1px solid rgba(245,241,232,0.08);border-radius:5px;padding:5px 10px;font-size:11px;color:#6a665f;cursor:pointer;white-space:nowrap;"
                            onmouseover="this.style.borderColor='rgba(122,149,200,0.3)';this.style.color='#7a95c8'" onmouseout="this.style.borderColor='rgba(245,241,232,0.08)';this.style.color='#6a665f'">
                        Mark read
                    </button>
                </form>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if ($notifications->hasPages())
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;">
        <span style="font-size:12px;color:#6a665f;">Showing {{ $notifications->firstItem() }}–{{ $notifications->lastItem() }} of {{ $notifications->total() }}</span>
        <div style="display:flex;gap:4px;">
            @if ($notifications->onFirstPage())
                <span style="padding:6px 12px;font-size:12px;color:#6a665f;background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:5px;">← Prev</span>
            @else
                <a href="{{ $notifications->previousPageUrl() }}" style="padding:6px 12px;font-size:12px;color:#a8a39c;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:5px;text-decoration:none;">← Prev</a>
            @endif
            @if ($notifications->hasMorePages())
                <a href="{{ $notifications->nextPageUrl() }}" style="padding:6px 12px;font-size:12px;color:#a8a39c;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:5px;text-decoration:none;">Next →</a>
            @else
                <span style="padding:6px 12px;font-size:12px;color:#6a665f;background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:5px;">Next →</span>
            @endif
        </div>
    </div>
    @endif
@endif

@endsection
