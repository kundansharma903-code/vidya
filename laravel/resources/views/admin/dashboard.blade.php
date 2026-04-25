@extends('layouts.admin')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')

@php $user = Auth::user(); @endphp

{{-- Welcome --}}
<div style="margin-bottom:28px;">
    <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px 0;">
        {{ $greeting }}, {{ explode(' ', $user->name)[0] }}
    </h1>
    <p style="font-size:14px;color:#a8a39c;margin:0;">
        Here's what's happening at {{ $currentInstitute->name ?? 'your institute' }} today.
    </p>
</div>

{{-- KPI Cards --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">

    @php
        $kpis = [
            ['label' => 'TOTAL COURSES',   'value' => $stats['total_courses'],  'trend' => null],
            ['label' => 'ACTIVE BATCHES',  'value' => $stats['active_batches'], 'trend' => null],
            ['label' => 'TOTAL STUDENTS',  'value' => $stats['total_students'], 'trend' => null],
            ['label' => 'ACTIVE STAFF',    'value' => $stats['active_staff'],   'trend' => null],
        ];
    @endphp

    @foreach ($kpis as $kpi)
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:20px;">
        <div style="font-size:11px;font-weight:600;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin-bottom:10px;">{{ $kpi['label'] }}</div>
        <div style="font-size:32px;font-weight:700;color:#f5f1e8;letter-spacing:-0.64px;line-height:1;">{{ number_format($kpi['value']) }}</div>
        <div style="margin-top:8px;display:flex;align-items:center;gap:4px;">
            <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#7fb685" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
            </svg>
            <span style="font-size:12px;color:#7fb685;">{{ $kpi['value'] > 0 ? 'Active' : 'None yet' }}</span>
        </div>
    </div>
    @endforeach

</div>

{{-- Quick Actions --}}
<div style="margin-bottom:24px;">
    <h2 style="font-size:13px;font-weight:600;color:#a8a39c;letter-spacing:0.52px;text-transform:uppercase;margin:0 0 14px 0;">Quick Actions</h2>
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">

        @php
            $actions = [
                ['title' => 'Add Course',        'sub' => 'Create a new course',      'route' => '#',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>'],
                ['title' => 'Add Batch',         'sub' => 'Set up a new batch',        'route' => '#',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>'],
                ['title' => 'Import Students',   'sub' => 'Bulk upload via CSV',       'route' => '#',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>'],
                ['title' => 'Create Staff',      'sub' => 'Add a teacher or typist',   'route' => '#',
                 'svg' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>'],
            ];
        @endphp

        @foreach ($actions as $action)
        <a href="{{ $action['route'] }}" style="display:flex;align-items:flex-start;gap:14px;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px;text-decoration:none;transition:border-color 0.15s;"
           onmouseover="this.style.borderColor='rgba(122,149,200,0.30)'"
           onmouseout="this.style.borderColor='rgba(245,241,232,0.08)'">
            <div style="width:36px;height:36px;background:rgba(122,149,200,0.12);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#7a95c8" stroke-width="1.75">
                    {!! $action['svg'] !!}
                </svg>
            </div>
            <div>
                <div style="font-size:13px;font-weight:500;color:#f5f1e8;margin-bottom:3px;">{{ $action['title'] }}</div>
                <div style="font-size:12px;color:#6a665f;">{{ $action['sub'] }}</div>
            </div>
        </a>
        @endforeach

    </div>
</div>

{{-- Bottom row: Recent Activity + System Health --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">

    {{-- Recent Activity --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:20px;">
        <h2 style="font-size:13px;font-weight:600;color:#a8a39c;letter-spacing:0.52px;text-transform:uppercase;margin:0 0 18px 0;">Recent Activity</h2>

        @if ($recentActivity->isEmpty())
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:32px 0;">
                <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="rgba(245,241,232,0.15)" stroke-width="1.5" style="margin-bottom:10px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span style="font-size:13px;color:#6a665f;">No activity yet</span>
            </div>
        @else
            <div style="display:flex;flex-direction:column;gap:0;">
                @foreach ($recentActivity as $log)
                <div style="display:flex;align-items:flex-start;gap:14px;padding:12px 0;border-bottom:1px solid rgba(245,241,232,0.05);">
                    <div style="width:8px;height:8px;background:#7a95c8;border-radius:50%;margin-top:5px;flex-shrink:0;"></div>
                    <div style="flex:1;">
                        <span style="font-size:13px;color:#f5f1e8;">{{ $log->description ?? ($log->action ?? 'Activity recorded') }}</span>
                    </div>
                    <span style="font-size:11px;color:#6a665f;white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- System Health --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:20px;">
        <h2 style="font-size:13px;font-weight:600;color:#a8a39c;letter-spacing:0.52px;text-transform:uppercase;margin:0 0 18px 0;">System Health</h2>

        @php
            $healthItems = [
                ['label' => 'Database',    'status' => 'Healthy',  'color' => '#7fb685', 'dot' => '#7fb685'],
                ['label' => 'Gemini AI',   'status' => 'Online',   'color' => '#7fb685', 'dot' => '#7fb685'],
                ['label' => 'Last Backup', 'status' => '2hr ago',  'color' => '#a8a39c', 'dot' => '#7a95c8'],
                ['label' => 'Storage',     'status' => '67% used', 'color' => '#c9a96e', 'dot' => '#c9a96e'],
            ];
        @endphp

        <div style="display:flex;flex-direction:column;gap:14px;">
            @foreach ($healthItems as $item)
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="width:8px;height:8px;background:{{ $item['dot'] }};border-radius:50%;flex-shrink:0;"></span>
                    <span style="font-size:13px;color:#a8a39c;">{{ $item['label'] }}</span>
                </div>
                <span style="font-size:12px;font-weight:500;color:{{ $item['color'] }};">{{ $item['status'] }}</span>
            </div>
            @endforeach
        </div>

        {{-- Storage bar --}}
        <div style="margin-top:20px;">
            <div style="height:4px;background:rgba(245,241,232,0.08);border-radius:2px;overflow:hidden;">
                <div style="height:100%;width:67%;background:linear-gradient(90deg,#7a95c8,#c9a96e);border-radius:2px;"></div>
            </div>
        </div>

    </div>

</div>

@endsection
