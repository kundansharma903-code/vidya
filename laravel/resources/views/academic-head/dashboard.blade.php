@extends('layouts.academic-head')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
@php
    $hour = (int) now()->format('H');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

    $hsColor = $healthScore >= 75 ? '#7fb685' : ($healthScore >= 50 ? '#d4a574' : '#c87064');
    $hsStroke = $healthScore >= 75 ? '127,182,133' : ($healthScore >= 50 ? '212,165,116' : '200,112,100');

    // SVG circle math: r=54, circumference=339.3
    $circ = 339.3;
    $offset = $circ - ($healthScore / 100 * $circ);

    $subjectColors = ['P'=>'#7a95c8','C'=>'#d4a574','B'=>'#7fb685','Z'=>'#c87064','M'=>'#a8a39c'];
@endphp

<div style="max-width:1060px;">

    {{-- Greeting --}}
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">{{ $greeting }}, Dr. Meera 👋</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Institute-wide academic overview · All subjects · All batches</p>
    </div>

    {{-- Top row: Health Score + 4 KPIs --}}
    <div style="display:grid;grid-template-columns:280px 1fr;gap:16px;margin-bottom:18px;">

        {{-- Health Score Card --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:12px;padding:22px 24px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
            <p style="font-size:10px;font-weight:600;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;align-self:flex-start;">Institute Health Score</p>

            {{-- Circular gauge --}}
            <div style="position:relative;width:120px;height:120px;margin-bottom:14px;">
                <svg width="120" height="120" viewBox="0 0 120 120" style="transform:rotate(-90deg);">
                    <circle cx="60" cy="60" r="54" fill="none" stroke="rgba(245,241,232,0.06)" stroke-width="10"/>
                    <circle cx="60" cy="60" r="54" fill="none"
                            stroke="{{ $hsColor }}" stroke-width="10"
                            stroke-dasharray="{{ $circ }}"
                            stroke-dashoffset="{{ $offset }}"
                            stroke-linecap="round"/>
                </svg>
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <span style="font-size:28px;font-weight:800;color:{{ $hsColor }};letter-spacing:-0.56px;line-height:1;">{{ $healthScore }}</span>
                    <span style="font-size:10px;color:#6a665f;">/100</span>
                </div>
            </div>

            {{-- 4 sub-metrics --}}
            <div style="width:100%;display:flex;flex-direction:column;gap:7px;">
                @foreach([
                    ['Curriculum','📋',$curriculumCoverage],
                    ['Test Quality','✎',$testQuality],
                    ['Teachers','👥',$teacherEffectiveness],
                    ['Retention','♦',$studentRetention],
                ] as [$label,$icon,$val])
                    @php $vc = $val >= 70 ? '#7fb685' : ($val >= 45 ? '#d4a574' : '#c87064'); @endphp
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-size:11px;">{{ $icon }}</span>
                            <span style="font-size:11px;color:#a8a39c;">{{ $label }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="width:64px;height:3px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                                <div style="height:100%;background:{{ $vc }};width:{{ $val }}%;"></div>
                            </div>
                            <span style="font-size:11px;font-weight:600;color:{{ $vc }};min-width:28px;text-align:right;">{{ $val }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 4 KPI cards --}}
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;">
            <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
                <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 8px;">Tests This Month</p>
                <p style="font-size:32px;font-weight:700;color:#7a95c8;letter-spacing:-0.64px;margin:0 0 4px;">{{ $testsThisMonth }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;">Analyzed results</p>
            </div>
            <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
                <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 8px;">Class Average</p>
                <p style="font-size:32px;font-weight:700;color:#d4a574;letter-spacing:-0.64px;margin:0 0 4px;">{{ $classAvg }}%</p>
                <p style="font-size:11px;color:#6a665f;margin:0;">Avg mastery score</p>
            </div>
            <div style="background:#14141b;border:1px solid rgba(200,112,100,0.12);border-radius:8px;padding:18px 20px;">
                <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 8px;">Weak Topics</p>
                <p style="font-size:32px;font-weight:700;color:#c87064;letter-spacing:-0.64px;margin:0 0 4px;">{{ $weakTopicsCount }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;">Topics below 40%</p>
            </div>
            <div style="background:#14141b;border:1px solid rgba(200,112,100,0.12);border-radius:8px;padding:18px 20px;">
                <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 8px;">At-Risk Students</p>
                <p style="font-size:32px;font-weight:700;color:#c87064;letter-spacing:-0.64px;margin:0 0 4px;">{{ $atRiskCount }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;">Avg mastery &lt; 40%</p>
            </div>
        </div>
    </div>

    {{-- Subject Performance row --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 24px;margin-bottom:18px;">
        <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Subject Performance</p>
        <div style="display:grid;grid-template-columns:repeat({{ count($subjectPerformance) }},1fr);gap:16px;">
            @foreach ($subjectPerformance as $sp)
                @php
                    $color = $subjectColors[$sp['code']] ?? '#a8a39c';
                    $mc    = $sp['avg'] >= 70 ? '#7fb685' : ($sp['avg'] >= 40 ? '#d4a574' : '#c87064');
                @endphp
                <div style="padding:14px 16px;background:#0f0f14;border-radius:8px;border:1px solid rgba(245,241,232,0.05);">
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                        <div style="width:36px;height:36px;border-radius:6px;background:rgba({{ implode(',',sscanf($color,'#%02x%02x%02x')) }},0.15);display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:800;color:{{ $color }};flex-shrink:0;">
                            {{ $sp['code'] }}
                        </div>
                        <div>
                            <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">{{ $sp['name'] }}</p>
                            <p style="font-size:10px;color:#6a665f;margin:0;">{{ $sp['covered'] }}/{{ $sp['total'] }} topics</p>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                        <span style="font-size:11px;color:#6a665f;">Avg Mastery</span>
                        <span style="font-size:14px;font-weight:700;color:{{ $mc }};">{{ $sp['avg'] }}%</span>
                    </div>
                    <div style="height:5px;background:rgba(245,241,232,0.06);border-radius:3px;overflow:hidden;">
                        <div style="height:100%;background:{{ $mc }};width:{{ $sp['avg'] }}%;"></div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:6px;">
                        <span style="font-size:10px;color:#6a665f;">Coverage</span>
                        <span style="font-size:10px;color:#a8a39c;">{{ $sp['pct'] }}%</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Bottom row: Coverage bars + Alerts --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        {{-- Curriculum Coverage bars --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0;">Curriculum Coverage</p>
                <a href="{{ route('academic-head.curriculum-coverage') }}" style="font-size:11px;color:#7a95c8;">Full report →</a>
            </div>
            @foreach ($subjectPerformance as $sp)
                @php $color = $subjectColors[$sp['code']] ?? '#a8a39c'; @endphp
                <div style="margin-bottom:12px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-size:11px;font-weight:700;color:{{ $color }};">{{ $sp['code'] }}</span>
                            <span style="font-size:12px;color:#f5f1e8;">{{ $sp['name'] }}</span>
                        </div>
                        <span style="font-size:12px;font-weight:600;color:#a8a39c;">{{ $sp['pct'] }}%</span>
                    </div>
                    <div style="height:5px;background:rgba(245,241,232,0.06);border-radius:3px;overflow:hidden;">
                        <div style="height:100%;background:{{ $color }};width:{{ $sp['pct'] }}%;opacity:0.7;"></div>
                    </div>
                    <p style="font-size:10px;color:#6a665f;margin:3px 0 0;">{{ $sp['covered'] }} of {{ $sp['total'] }} topics tested</p>
                </div>
            @endforeach
        </div>

        {{-- Academic Alerts --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Academic Alerts</p>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach ($alerts as $alert)
                    @php
                        $alertColor = $alert['type'] === 'danger' ? '#c87064' : ($alert['type'] === 'warning' ? '#d4a574' : '#7fb685');
                        $alertBg    = $alert['type'] === 'danger' ? 'rgba(200,112,100,0.08)' : ($alert['type'] === 'warning' ? 'rgba(212,165,116,0.08)' : 'rgba(127,182,133,0.08)');
                        $alertIcon  = $alert['type'] === 'danger' ? '✕' : ($alert['type'] === 'warning' ? '⚠' : '✓');
                    @endphp
                    <div style="display:flex;align-items:flex-start;gap:10px;background:{{ $alertBg }};border-radius:6px;padding:10px 12px;border:1px solid {{ $alertColor }}18;">
                        <span style="font-size:12px;color:{{ $alertColor }};flex-shrink:0;margin-top:1px;">{{ $alertIcon }}</span>
                        <p style="font-size:12px;color:#f5f1e8;margin:0;line-height:1.5;">{{ $alert['msg'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
