@extends('layouts.reception')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div style="max-width:1040px;">

    {{-- Page header --}}
    <div style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Reception Desk</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">{{ now()->format('l, d F Y') }} · Search students or look up test results</p>
    </div>

    {{-- PRIMARY: Quick Student Lookup --}}
    <div style="background:rgba(200,112,100,0.07);border:1px solid rgba(200,112,100,0.25);border-radius:12px;padding:28px 32px;margin-bottom:20px;">
        <p style="font-size:11px;font-weight:700;color:#c87064;text-transform:uppercase;letter-spacing:1.2px;margin:0 0 10px;">Quick Student Lookup</p>
        <form method="GET" action="{{ route('reception.students') }}" style="display:flex;gap:10px;">
            <input name="search" placeholder="Type roll number or student name…"
                autofocus
                style="flex:1;background:#0f0f14;border:1px solid rgba(200,112,100,0.3);border-radius:8px;padding:12px 16px;color:#f5f1e8;font-size:15px;outline:none;letter-spacing:0.2px;"
                onfocus="this.style.borderColor='#c87064'" onblur="this.style.borderColor='rgba(200,112,100,0.3)'">
            <button type="submit" style="background:#c87064;border:none;border-radius:8px;padding:12px 24px;color:#fff;font-size:14px;font-weight:700;cursor:pointer;letter-spacing:0.2px;">
                Search
            </button>
        </form>

        {{-- Batch quick-filter chips --}}
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:12px;">
            @foreach($batches as $b)
            <a href="{{ route('reception.students', ['batch_id' => $b->id]) }}"
               style="font-size:11px;color:#a8a39c;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:20px;padding:4px 12px;text-decoration:none;transition:all 0.15s;"
               onmouseover="this.style.borderColor='#c87064';this.style.color='#c87064'" onmouseout="this.style.borderColor='rgba(245,241,232,0.08)';this.style.color='#a8a39c'">
                {{ $b->code }}
            </a>
            @endforeach
        </div>
    </div>

    {{-- 4 KPIs --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
        @foreach([
            ['Total Students',    $totalStudents,  '#f5f1e8'],
            ['Analyzed Tests',    $totalTests,     '#c87064'],
            ['Walk-ins Today',    $walkInsToday,   '#d4a574'],
            ['Walk-ins This Week',$walkInsWeek,    '#7a95c8'],
        ] as [$lbl,$val,$col])
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:18px 20px;">
            <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1.1px;margin:0 0 6px;">{{ $lbl }}</p>
            <p style="font-size:28px;font-weight:800;color:{{ $col }};margin:0;letter-spacing:-0.7px;">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        {{-- Recent Walk-ins Today --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0;">Today's Walk-ins</h2>
                <a href="{{ route('reception.walk-ins') }}" style="font-size:11px;color:#c87064;text-decoration:none;">View all →</a>
            </div>
            @forelse($recentWalkIns as $log)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                <div>
                    <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">{{ $log->student_name }}</p>
                    <p style="font-size:10px;color:#6a665f;margin:0;">{{ $log->roll_number }}{{ $log->test_name ? ' · '.$log->test_name : '' }}</p>
                </div>
                <p style="font-size:10px;color:#4a4740;margin:0;">{{ \Carbon\Carbon::parse($log->viewed_at)->format('H:i') }}</p>
            </div>
            @empty
            <div style="padding:20px 0;text-align:center;">
                <p style="font-size:13px;color:#4a4740;margin:0;">No walk-ins yet today.</p>
                <p style="font-size:11px;color:#3d3a35;margin:4px 0 0;">Use the search above to start a lookup.</p>
            </div>
            @endforelse
        </div>

        {{-- Latest Tests Available --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0;">Latest Tests</h2>
                <a href="{{ route('reception.tests') }}" style="font-size:11px;color:#c87064;text-decoration:none;">View all →</a>
            </div>
            @forelse($latestTests as $test)
            <a href="{{ route('reception.test-results', $test->id) }}"
               style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(245,241,232,0.04);text-decoration:none;"
               onmouseover="this.style.background='rgba(245,241,232,0.02)'" onmouseout="this.style.background='transparent'">
                <div>
                    <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">{{ $test->name }}</p>
                    <p style="font-size:10px;color:#6a665f;margin:0;">{{ $test->test_code }} · {{ \Carbon\Carbon::parse($test->test_date)->format('d M Y') }}</p>
                </div>
                <span style="font-size:11px;color:#c87064;font-weight:600;">Results →</span>
            </a>
            @empty
            <p style="font-size:13px;color:#4a4740;padding:20px 0;text-align:center;">No analyzed tests yet.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
