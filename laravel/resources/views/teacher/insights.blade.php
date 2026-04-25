@extends('layouts.teacher')
@section('title', 'Class Insights')
@section('breadcrumb', 'Class Insights')

@section('content')
@php
    $strongPct  = $totalStudents > 0 ? round($dist['strong']  / $totalStudents * 100) : 0;
    $avgPct     = $totalStudents > 0 ? round($dist['average'] / $totalStudents * 100) : 0;
    $weakPct    = $totalStudents > 0 ? round($dist['weak']    / $totalStudents * 100) : 0;
@endphp

<div style="max-width:1040px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Class Insights</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">{{ $subject?->name }} · Overall performance analysis</p>
    </div>

    {{-- Mastery Distribution --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;margin-bottom:18px;">
        <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 18px;">Mastery Distribution</p>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:20px;">
            @foreach([['Strong','≥70%','#7fb685','rgba(127,182,133,0.12)',$dist['strong'],$strongPct],['Average','40–69%','#d4a574','rgba(212,165,116,0.12)',$dist['average'],$avgPct],['Weak','<40%','#c87064','rgba(200,112,100,0.12)',$dist['weak'],$weakPct]] as [$label,$range,$color,$bg,$count,$pct])
                <div style="background:{{ $bg }};border-radius:8px;padding:16px 18px;border:1px solid {{ $color }}20;">
                    <p style="font-size:26px;font-weight:700;color:{{ $color }};margin:0 0 2px;letter-spacing:-0.52px;">{{ $count }}</p>
                    <p style="font-size:12px;font-weight:600;color:{{ $color }};margin:0 0 2px;">{{ $label }}</p>
                    <p style="font-size:11px;color:#6a665f;margin:0;">{{ $range }} · {{ $pct }}%</p>
                </div>
            @endforeach
        </div>

        {{-- Stacked bar --}}
        <div style="height:10px;border-radius:5px;overflow:hidden;display:flex;">
            @if ($strongPct > 0)
                <div style="width:{{ $strongPct }}%;background:#7fb685;"></div>
            @endif
            @if ($avgPct > 0)
                <div style="width:{{ $avgPct }}%;background:#d4a574;"></div>
            @endif
            @if ($weakPct > 0)
                <div style="width:{{ $weakPct }}%;background:#c87064;"></div>
            @endif
            @if ($strongPct + $avgPct + $weakPct < 100)
                <div style="flex:1;background:rgba(245,241,232,0.06);"></div>
            @endif
        </div>
    </div>

    {{-- Toppers + At-Risk --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">

        {{-- Toppers --}}
        <div style="background:#14141b;border:1px solid rgba(127,182,133,0.15);border-radius:10px;padding:20px 22px;">
            <p style="font-size:11px;font-weight:500;color:#7fb685;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Top Performers</p>
            @forelse ($toppers as $i => $s)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="width:22px;height:22px;border-radius:50%;background:rgba(127,182,133,0.15);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#7fb685;flex-shrink:0;">{{ $i+1 }}</span>
                        <div>
                            <p style="font-size:12px;font-weight:500;color:#f5f1e8;margin:0;">{{ $s->name }}</p>
                            <span style="font-size:10px;color:#6a665f;">{{ $s->batch_name }}</span>
                        </div>
                    </div>
                    <span style="background:rgba(127,182,133,0.12);border-radius:9999px;padding:3px 10px;font-size:12px;font-weight:600;color:#7fb685;">{{ $s->avg_m }}%</span>
                </div>
            @empty
                <p style="font-size:13px;color:#6a665f;margin:20px 0;text-align:center;">No data yet.</p>
            @endforelse
        </div>

        {{-- At-risk --}}
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.2);border-radius:10px;padding:20px 22px;">
            <p style="font-size:11px;font-weight:500;color:#c87064;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">At-Risk Students</p>
            @forelse ($atRisk as $s)
                <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:8px;height:8px;border-radius:50%;background:#c87064;flex-shrink:0;"></div>
                        <div>
                            <p style="font-size:12px;font-weight:500;color:#f5f1e8;margin:0;">{{ $s->name }}</p>
                            <span style="font-size:10px;color:#6a665f;">{{ $s->batch_name }}</span>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span style="background:rgba(200,112,100,0.12);border-radius:9999px;padding:3px 10px;font-size:12px;font-weight:600;color:#c87064;">{{ $s->avg_m }}%</span>
                        <a href="{{ route('teacher.students.detail', $s->id) }}" style="font-size:10px;color:#7a95c8;">→</a>
                    </div>
                </div>
            @empty
                <div style="padding:20px 0;text-align:center;">
                    <p style="font-size:22px;margin:0 0 6px;">🎉</p>
                    <p style="font-size:13px;color:#7fb685;margin:0;">No at-risk students!</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Test Performance Over Time --}}
    @if ($recentTests->isNotEmpty())
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 24px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Test Performance Trend</p>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach ($recentTests->reverse() as $t)
                    @php
                        $pct = $t->max_marks > 0 ? round($t->avg_marks / $t->max_marks * 100) : 0;
                        $bc = $pct >= 60 ? '#7fb685' : ($pct >= 35 ? '#d4a574' : '#c87064');
                    @endphp
                    <div style="display:grid;grid-template-columns:100px 1fr 80px 60px;gap:12px;align-items:center;">
                        <span style="font-size:10px;font-weight:600;color:#a8a39c;font-family:monospace;">{{ $t->test_code }}</span>
                        <div style="height:6px;background:rgba(245,241,232,0.06);border-radius:3px;overflow:hidden;">
                            <div style="height:100%;background:{{ $bc }};width:{{ $pct }}%;"></div>
                        </div>
                        <span style="font-size:12px;font-weight:600;color:{{ $bc }};text-align:right;">{{ $t->avg_marks }}</span>
                        <span style="font-size:11px;color:#6a665f;">/ {{ $t->max_marks }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection
