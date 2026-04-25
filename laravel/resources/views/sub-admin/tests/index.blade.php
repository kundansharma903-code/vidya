@extends('layouts.sub-admin')
@section('title', 'All Tests')
@section('breadcrumb', 'All Tests')

@php
$routeExists = function(string $name): bool {
    try { route($name); return true; } catch (\Exception $e) { return false; }
};

$statusConfig = [
    'scheduled'           => ['label' => 'Scheduled',     'dot' => '#6ab0b2', 'bg' => 'rgba(106,176,178,0.15)',  'btn' => '#6ab0b2', 'action' => 'Edit'],
    'conducted'           => ['label' => 'Pending Upload', 'dot' => '#d4a574', 'bg' => 'rgba(212,165,116,0.15)', 'btn' => '#d4a574', 'action' => 'Upload'],
    'responses_uploaded'  => ['label' => 'Pending Upload', 'dot' => '#d4a574', 'bg' => 'rgba(212,165,116,0.15)', 'btn' => '#d4a574', 'action' => 'Upload'],
    'analyzed'            => ['label' => 'Analyzed',       'dot' => '#7fb685', 'bg' => 'rgba(127,182,133,0.15)',  'btn' => '#7fb685', 'action' => 'View'],
    'draft'               => ['label' => 'Draft',          'dot' => '#a8a39c', 'bg' => 'rgba(168,163,156,0.12)', 'btn' => '#a8a39c', 'action' => 'Edit'],
    'blueprint_ready'     => ['label' => 'Pending Upload', 'dot' => '#d4a574', 'bg' => 'rgba(212,165,116,0.15)', 'btn' => '#d4a574', 'action' => 'Upload'],
    'archived'            => ['label' => 'Archived',       'dot' => '#6a665f', 'bg' => 'rgba(106,102,95,0.12)',  'btn' => '#6a665f', 'action' => 'View'],
];

$tabs = [
    'all'            => ['label' => 'All',            'count' => $counts['all']],
    'pending_upload' => ['label' => 'Pending Upload', 'count' => $counts['pending_upload']],
    'analyzed'       => ['label' => 'Analyzed',       'count' => $counts['analyzed']],
    'scheduled'      => ['label' => 'Scheduled',      'count' => $counts['scheduled']],
];
@endphp

@section('content')

    {{-- Page header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;">
        <div>
            <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px;">All Tests</h1>
            <p style="font-size:14px;color:#a8a39c;margin:0;">Manage all tests · Click any test to upload results</p>
        </div>
        <a href="{{ route('sub-admin.tests.create') }}"
           style="background:#7a95c8;border-radius:6px;padding:11px 16px;font-size:13px;font-weight:600;color:#14141b;text-decoration:none;display:flex;align-items:center;gap:8px;">
            <span style="font-weight:700;">+</span> Create Test
        </a>
    </div>

    @if (session('success'))
        <div style="background:rgba(127,182,133,0.12);border:1px solid rgba(127,182,133,0.3);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#7fb685;">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- KPI row --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px;">
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 6px;">Total Tests</p>
            <p style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 6px;">{{ $counts['all'] }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">This year</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 6px;">Pending Upload</p>
            <p style="font-size:26px;font-weight:700;color:#d4a574;letter-spacing:-0.52px;margin:0 0 6px;">{{ $counts['pending_upload'] }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Result Excel needed</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 6px;">Analyzed</p>
            <p style="font-size:26px;font-weight:700;color:#7fb685;letter-spacing:-0.52px;margin:0 0 6px;">{{ $counts['analyzed'] }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Reports ready</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 6px;">Scheduled</p>
            <p style="font-size:26px;font-weight:700;color:#7a95c8;letter-spacing:-0.52px;margin:0 0 6px;">{{ $counts['scheduled'] }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Upcoming tests</p>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="display:flex;gap:8px;margin-bottom:22px;">
        @foreach ($tabs as $key => $tabData)
            @php $isActive = $tab === $key; @endphp
            <a href="{{ route('sub-admin.tests.index', ['tab' => $key]) }}"
               style="display:flex;align-items:center;gap:5px;padding:7px 12px;border-radius:6px;text-decoration:none;
                      background:{{ $isActive ? 'rgba(122,149,200,0.15)' : '#14141b' }};
                      border:1px solid {{ $isActive ? 'rgba(122,149,200,0.3)' : 'rgba(245,241,232,0.1)' }};">
                <span style="font-size:12px;font-weight:{{ $isActive ? '600' : '500' }};color:{{ $isActive ? '#7a95c8' : '#a8a39c' }};">
                    {{ $tabData['label'] }}
                </span>
                <span style="background:{{ $isActive ? '#7a95c8' : '#0f0f14' }};color:{{ $isActive ? '#14141b' : '#a8a39c' }};font-size:10px;font-weight:700;padding:1px 6px;border-radius:9999px;">
                    {{ $tabData['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Table --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;overflow:hidden;">

        {{-- Header --}}
        <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:130px 1fr 110px 120px 90px 140px 110px;gap:12px;padding:14px 20px;align-items:center;">
            @foreach(['CODE','NAME','DATE','BATCHES','STUDENTS','STATUS','ACTION'] as $col)
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;">{{ $col }}</span>
            @endforeach
        </div>

        {{-- Rows --}}
        @forelse ($tests as $test)
            @php
                $cfg      = $statusConfig[$test->status] ?? $statusConfig['draft'];
                $dateLabel = match(true) {
                    \Carbon\Carbon::parse($test->test_date)->isFuture()    => \Carbon\Carbon::parse($test->test_date)->format('d M Y'),
                    \Carbon\Carbon::parse($test->test_date)->isToday()     => 'Today',
                    \Carbon\Carbon::parse($test->test_date)->isYesterday() => 'Yesterday',
                    default => \Carbon\Carbon::parse($test->test_date)->diffForHumans(),
                };
                $actionUrl = match($cfg['action']) {
                    'Upload' => $routeExists('sub-admin.results.upload') ? route('sub-admin.results.upload') . '?test_id=' . $test->id : '#',
                    'View'   => '#',
                    'Edit'   => $routeExists('sub-admin.tests.create') ? route('sub-admin.tests.create') : '#',
                    default  => '#',
                };
            @endphp
            <div style="display:grid;grid-template-columns:130px 1fr 110px 120px 90px 140px 110px;gap:12px;padding:14px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.05);"
                 onmouseover="this.style.background='rgba(26,26,36,0.4)'" onmouseout="this.style.background=''">

                <div>
                    <span style="background:#0f0f14;border-radius:4px;padding:4px 8px;font-size:11px;font-weight:500;color:#a8a39c;">{{ $test->test_code }}</span>
                </div>

                <div style="overflow:hidden;">
                    <span style="font-size:13px;font-weight:500;color:#f5f1e8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">{{ $test->name }}</span>
                </div>

                <div>
                    <span style="font-size:12px;color:#a8a39c;">{{ $dateLabel }}</span>
                </div>

                <div>
                    <span style="font-size:12px;font-weight:500;color:#a8a39c;">{{ $test->batch_count }} {{ Str::plural('batch', $test->batch_count) }}</span>
                </div>

                <div style="display:flex;align-items:center;gap:4px;">
                    <span style="font-size:11px;">👥</span>
                    <span style="font-size:12px;font-weight:500;color:#a8a39c;">{{ $test->student_count }}</span>
                </div>

                <div>
                    <span style="background:{{ $cfg['bg'] }};border-radius:9999px;padding:4px 10px;display:inline-flex;align-items:center;gap:6px;">
                        <span style="width:6px;height:6px;background:{{ $cfg['dot'] }};border-radius:50%;display:inline-block;flex-shrink:0;"></span>
                        <span style="font-size:11px;font-weight:500;color:{{ $cfg['dot'] }};">{{ $cfg['label'] }}</span>
                    </span>
                </div>

                <div>
                    <a href="{{ $actionUrl }}"
                       style="background:{{ $cfg['btn'] }};border-radius:6px;padding:6px 10px;font-size:11px;font-weight:600;color:#14141b;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">
                        {{ $cfg['action'] }} <span style="font-weight:700;">→</span>
                    </a>
                </div>

            </div>
        @empty
            <div style="padding:48px 20px;text-align:center;">
                <p style="font-size:14px;color:#6a665f;margin:0 0 12px;">No tests found{{ $tab !== 'all' ? ' in this tab' : '' }}.</p>
                <a href="{{ route('sub-admin.tests.create') }}" style="font-size:13px;color:#7a95c8;text-decoration:none;">+ Create your first test →</a>
            </div>
        @endforelse

    </div>

@endsection
