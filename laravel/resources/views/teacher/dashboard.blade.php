@extends('layouts.teacher')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
@php
    $hour = (int) now()->format('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
    $firstName = explode(' ', $user->name)[0];
    $subjectLetter = $subject ? strtoupper(substr($subject->name, 0, 1)) : 'P';
    $subjectName   = $subject ? $subject->name : 'Physics';
@endphp

<div style="max-width:1040px;">

    {{-- Greeting --}}
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">{{ $greeting }}, {{ $firstName }} 👋</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Here's how your class is performing today.</p>
    </div>

    {{-- Subject Banner --}}
    <div style="background:rgba(122,149,200,0.07);border:1px solid rgba(122,149,200,0.18);border-radius:10px;padding:18px 22px;margin-bottom:20px;display:flex;align-items:center;gap:18px;">
        <div style="width:44px;height:44px;border-radius:8px;background:rgba(122,149,200,0.15);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:#7a95c8;flex-shrink:0;letter-spacing:-0.4px;">
            {{ $subjectLetter }}
        </div>
        <div style="flex:1;">
            <p style="font-size:11px;font-weight:600;color:#7a95c8;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 2px;">Your Subject</p>
            <p style="font-size:17px;font-weight:700;color:#f5f1e8;margin:0;">{{ $subjectName }}</p>
        </div>
        <div style="display:flex;gap:28px;align-items:center;">
            <div style="text-align:center;">
                <p style="font-size:22px;font-weight:700;color:#7a95c8;margin:0;letter-spacing:-0.44px;">{{ $avgMastery }}%</p>
                <p style="font-size:10px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Avg Mastery</p>
            </div>
            <div style="width:1px;height:36px;background:rgba(122,149,200,0.15);"></div>
            <div style="text-align:center;">
                <p style="font-size:22px;font-weight:700;color:#c87064;margin:0;letter-spacing:-0.44px;">{{ $weakTopicsCount }}</p>
                <p style="font-size:10px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Weak Topics</p>
            </div>
            <div style="width:1px;height:36px;background:rgba(122,149,200,0.15);"></div>
            <div style="text-align:center;">
                <p style="font-size:22px;font-weight:700;color:#7fb685;margin:0;letter-spacing:-0.44px;">{{ $strongTopicsCount }}</p>
                <p style="font-size:10px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Strong Topics</p>
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;">
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">My Students</p>
            <p style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 4px;">{{ $studentCount }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Across all batches</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Tests Conducted</p>
            <p style="font-size:28px;font-weight:700;color:#7a95c8;letter-spacing:-0.56px;margin:0 0 4px;">{{ $testsCount }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Analyzed results</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Class Average</p>
            <p style="font-size:28px;font-weight:700;color:#d4a574;letter-spacing:-0.56px;margin:0 0 4px;">{{ $avgMastery }}%</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Mastery score</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.15);border-radius:8px;padding:18px 20px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">At-Risk</p>
            <p style="font-size:28px;font-weight:700;color:#c87064;letter-spacing:-0.56px;margin:0 0 4px;">{{ $atRiskCount }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Need attention</p>
        </div>
    </div>

    {{-- Two-panel row --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        {{-- Recent Tests --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0;">Recent Tests</p>
                <a href="{{ route('teacher.tests') }}" style="font-size:11px;color:#7a95c8;">View all →</a>
            </div>

            @forelse ($recentTests as $t)
                @php
                    $pct = $t->max_marks > 0 ? round($t->avg_marks / $t->max_marks * 100) : 0;
                    $scoreColor = $pct >= 60 ? '#7fb685' : ($pct >= 35 ? '#d4a574' : '#c87064');
                @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                    <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                        <span style="background:#0f0f14;border-radius:4px;padding:3px 7px;font-size:10px;font-weight:600;color:#a8a39c;font-family:monospace;flex-shrink:0;">{{ $t->test_code }}</span>
                        <span style="font-size:12px;color:#f5f1e8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $t->name }}</span>
                    </div>
                    <span style="background:rgba({{ $pct >= 60 ? '127,182,133' : ($pct >= 35 ? '212,165,116' : '200,112,100') }},0.12);border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:600;color:{{ $scoreColor }};flex-shrink:0;margin-left:10px;">
                        avg {{ $t->avg_marks }}
                    </span>
                </div>
            @empty
                <p style="font-size:13px;color:#6a665f;margin:20px 0;text-align:center;">No analyzed tests yet.</p>
            @endforelse
        </div>

        {{-- Weak Topics --}}
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.2);border-radius:10px;padding:20px 22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <p style="font-size:11px;font-weight:500;color:#c87064;letter-spacing:1px;text-transform:uppercase;margin:0;">Weak Topics</p>
                <a href="{{ route('teacher.weak-topics') }}" style="font-size:11px;color:#7a95c8;">See all →</a>
            </div>

            @forelse ($weakTopics as $wt)
                @php $mp = round($wt->avg_mastery); @endphp
                <div style="padding:8px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:12px;color:#f5f1e8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;">{{ $wt->topic_name }}</span>
                        <span style="background:rgba(200,112,100,0.12);border-radius:9999px;padding:2px 8px;font-size:11px;font-weight:600;color:#c87064;flex-shrink:0;margin-left:8px;">{{ $mp }}%</span>
                    </div>
                    <div style="height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                        <div style="height:100%;background:#c87064;width:{{ $mp }}%;border-radius:2px;"></div>
                    </div>
                </div>
            @empty
                <p style="font-size:13px;color:#7fb685;margin:20px 0;text-align:center;">No weak topics — class is doing well!</p>
            @endforelse
        </div>

    </div>

</div>
@endsection
