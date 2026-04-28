@extends('layouts.academic-head')
@section('title', $teacher->name . ' — Deep Dive')
@section('breadcrumb', 'Teacher Deep-dive')

@section('content')
@php
    $subjectColors = ['P'=>'#7a95c8','C'=>'#d4a574','B'=>'#7fb685','Z'=>'#c87064','M'=>'#a8a39c'];
    $sColor  = $subjectColors[$subjectCode] ?? '#a8a39c';
    $esColor = $effectivenessScore >= 70 ? '#7fb685' : ($effectivenessScore >= 45 ? '#d4a574' : '#c87064');
    $circ    = 339.3;
    $offset  = $circ - ($effectivenessScore / 100 * $circ);
    $lift    = round($classAvg - $instituteOverallAvg, 1);
    $liftColor = $lift >= 0 ? '#7fb685' : '#c87064';
@endphp

<div style="max-width:1060px;">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;font-size:13px;color:#6a665f;">
        <a href="{{ route('academic-head.teacher-effectiveness') }}" style="color:#6a665f;text-decoration:none;">Teacher Effectiveness</a>
        <span>›</span>
        <span style="color:#a8a39c;">{{ $teacher->name }}</span>
    </div>

    {{-- Profile header + Score banner --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:12px;padding:24px;margin-bottom:18px;display:flex;align-items:center;gap:24px;flex-wrap:wrap;">

        {{-- Avatar + Info --}}
        <div style="display:flex;align-items:center;gap:18px;flex:1;min-width:260px;">
            <div style="width:60px;height:60px;border-radius:50%;background:rgba(122,149,200,0.15);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:#7a95c8;flex-shrink:0;border:2px solid rgba(122,149,200,0.25);">
                {{ strtoupper(substr($teacher->name,0,1)) }}
            </div>
            <div>
                <h2 style="font-size:20px;font-weight:700;color:#f5f1e8;margin:0 0 6px;">{{ $teacher->name }}</h2>
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <span style="background:rgba({{ implode(',',sscanf($sColor,'#%02x%02x%02x')) }},0.15);border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:600;color:{{ $sColor }};">
                        {{ $subjectCode }} · {{ $subjectName }}
                    </span>
                    <span style="font-size:12px;color:#6a665f;">{{ $batches->count() }} {{ $batches->count() === 1 ? 'batch' : 'batches' }}</span>
                    <span style="font-size:12px;color:#6a665f;">{{ $studentIds->count() }} students</span>
                </div>
            </div>
        </div>

        <div style="width:1px;height:60px;background:rgba(245,241,232,0.08);flex-shrink:0;"></div>

        {{-- Score gauge --}}
        <div style="display:flex;align-items:center;gap:24px;flex-shrink:0;">
            <div style="position:relative;width:90px;height:90px;">
                <svg width="90" height="90" viewBox="0 0 120 120" style="transform:rotate(-90deg);">
                    <circle cx="60" cy="60" r="54" fill="none" stroke="rgba(245,241,232,0.06)" stroke-width="10"/>
                    <circle cx="60" cy="60" r="54" fill="none"
                            stroke="{{ $esColor }}" stroke-width="10"
                            stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $offset }}"
                            stroke-linecap="round"/>
                </svg>
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <span style="font-size:22px;font-weight:800;color:{{ $esColor }};letter-spacing:-0.44px;line-height:1;">{{ $effectivenessScore }}</span>
                    <span style="font-size:9px;color:#6a665f;">/100</span>
                </div>
            </div>
            <div>
                <p style="font-size:10px;color:#6a665f;text-transform:uppercase;letter-spacing:0.88px;margin:0 0 4px;">Effectiveness Score</p>
                <p style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0 0 4px;">Rank #{{ $rank }} of {{ $totalTeachers }}</p>
                <p style="font-size:13px;font-weight:600;color:{{ $liftColor }};margin:0;">
                    {{ $lift >= 0 ? '+' : '' }}{{ $lift }}pp vs institute avg
                </p>
            </div>
        </div>
    </div>

    {{-- 4 KPIs --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px;">
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Students</p>
            <p style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0;">{{ $studentIds->count() }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Across all batches</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(127,182,133,0.12);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Strong Topics</p>
            <p style="font-size:28px;font-weight:700;color:#7fb685;letter-spacing:-0.56px;margin:0;">{{ $strongCount }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Mastery ≥ 70%</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.12);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Weak Topics</p>
            <p style="font-size:28px;font-weight:700;color:#c87064;letter-spacing:-0.56px;margin:0;">{{ $weakCount }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Class avg &lt; 40%</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.12);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">At-Risk Students</p>
            <p style="font-size:28px;font-weight:700;color:#c87064;letter-spacing:-0.56px;margin:0;">{{ $atRiskCount }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">{{ $atRiskPct }}% of class</p>
        </div>
    </div>

    {{-- Strong + Weak Topics --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:18px;">

        {{-- Strong Topics --}}
        <div style="background:#14141b;border:1px solid rgba(127,182,133,0.15);border-radius:10px;padding:20px 22px;">
            <p style="font-size:11px;font-weight:500;color:#7fb685;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;">Top Strong Topics</p>
            @forelse ($strongTopics as $t)
                @php $mp = (int)round($t->avg_m); $diff = round($t->diff,1); @endphp
                <div style="padding:8px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <div style="min-width:0;flex:1;">
                            <span style="font-size:10px;color:#6a665f;font-family:monospace;">{{ $t->topic_code }}</span>
                            <span style="font-size:12px;color:#f5f1e8;margin-left:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:inline-block;max-width:140px;vertical-align:middle;">{{ $t->topic_name }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;margin-left:8px;">
                            <span style="font-size:12px;font-weight:700;color:#7fb685;">{{ $mp }}%</span>
                            <span style="font-size:10px;color:{{ $diff >= 0 ? '#7fb685' : '#c87064' }};">{{ $diff >= 0 ? '+' : '' }}{{ $diff }}pp</span>
                        </div>
                    </div>
                    <div style="height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                        <div style="height:100%;background:#7fb685;width:{{ $mp }}%;"></div>
                    </div>
                </div>
            @empty
                <p style="font-size:13px;color:#6a665f;margin:16px 0;text-align:center;">No strong topics yet.</p>
            @endforelse
        </div>

        {{-- Weak Topics --}}
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.2);border-radius:10px;padding:20px 22px;">
            <p style="font-size:11px;font-weight:500;color:#c87064;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;">Teaching Gaps</p>
            @forelse ($weakTopics as $t)
                @php $mp = (int)round($t->avg_m); $diff = round($t->diff,1); @endphp
                <div style="padding:8px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <div style="min-width:0;flex:1;">
                            <span style="font-size:10px;color:#6a665f;font-family:monospace;">{{ $t->topic_code }}</span>
                            <span style="font-size:12px;color:#f5f1e8;margin-left:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:inline-block;max-width:140px;vertical-align:middle;">{{ $t->topic_name }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;margin-left:8px;">
                            <span style="font-size:12px;font-weight:700;color:#c87064;">{{ $mp }}%</span>
                            <span style="font-size:10px;color:{{ $diff >= 0 ? '#7fb685' : '#c87064' }};">{{ $diff >= 0 ? '+' : '' }}{{ $diff }}pp</span>
                        </div>
                    </div>
                    <div style="height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                        <div style="height:100%;background:#c87064;width:{{ $mp }}%;"></div>
                    </div>
                </div>
            @empty
                <p style="font-size:13px;color:#7fb685;margin:16px 0;text-align:center;">No weak topics — great teaching!</p>
            @endforelse
        </div>

    </div>

    {{-- Batch-wise performance --}}
    @if (!empty($batchPerformance))
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;margin-bottom:18px;">
        <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:1fr 130px 90px 90px 90px;gap:10px;padding:11px 20px;">
            @foreach(['BATCH','CLASS AVG','WEAK TOPICS','AT-RISK','STUDENTS'] as $col)
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">{{ $col }}</span>
            @endforeach
        </div>
        @foreach ($batchPerformance as $bp)
            @php
                $bc = $bp['avg'] >= 70 ? '#7fb685' : ($bp['avg'] >= 40 ? '#d4a574' : '#c87064');
            @endphp
            <div style="display:grid;grid-template-columns:1fr 130px 90px 90px 90px;gap:10px;padding:13px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                 onmouseover="this.style.background='rgba(26,26,36,0.4)'" onmouseout="this.style.background=''">
                <div>
                    <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0;">{{ $bp['batch']->name }}</p>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="flex:1;height:5px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                        <div style="height:100%;background:{{ $bc }};width:{{ $bp['avg'] }}%;"></div>
                    </div>
                    <span style="font-size:13px;font-weight:700;color:{{ $bc }};min-width:32px;">{{ $bp['avg'] }}%</span>
                </div>
                <span style="font-size:13px;color:#c87064;">{{ $bp['weakTopics'] }}</span>
                <span style="font-size:13px;color:#c87064;">{{ $bp['atRisk'] }}</span>
                <span style="font-size:13px;color:#a8a39c;">{{ $bp['students'] }}</span>
            </div>
        @endforeach
    </div>
    @endif

    {{-- Performance Trend --}}
    @if (!empty($testTrend))
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;margin-bottom:18px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0;">Performance Trend</p>
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="display:flex;align-items:center;gap:5px;"><div style="width:10px;height:10px;border-radius:2px;background:#7a95c8;"></div><span style="font-size:11px;color:#a8a39c;">This class</span></div>
                <div style="display:flex;align-items:center;gap:5px;"><div style="width:10px;height:10px;border-radius:2px;background:rgba(245,241,232,0.15);"></div><span style="font-size:11px;color:#a8a39c;">Institute avg</span></div>
            </div>
        </div>
        <div style="display:flex;align-items:flex-end;gap:10px;height:100px;">
            @foreach ($testTrend as $tr)
                <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
                    <div style="width:100%;display:flex;gap:2px;align-items:flex-end;height:80px;">
                        {{-- Teacher bar --}}
                        <div style="flex:1;background:#7a95c8;border-radius:2px 2px 0 0;height:{{ max(2, $tr['teacherPct']) }}%;min-height:2px;position:relative;" title="{{ $tr['teacherPct'] }}%"></div>
                        {{-- Institute bar --}}
                        <div style="flex:1;background:rgba(245,241,232,0.12);border-radius:2px 2px 0 0;height:{{ max(2, $tr['instPct']) }}%;min-height:2px;" title="{{ $tr['instPct'] }}%"></div>
                    </div>
                    <span style="font-size:9px;color:#6a665f;font-family:monospace;text-align:center;white-space:nowrap;">{{ $tr['code'] }}</span>
                </div>
            @endforeach
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:6px;">
            <span style="font-size:10px;color:#6a665f;">Older tests →</span>
            <span style="font-size:10px;color:#6a665f;">Recent</span>
        </div>
    </div>
    @endif

    {{-- AI Action Items --}}
    <div style="background:rgba(122,149,200,0.06);border:1px solid rgba(122,149,200,0.2);border-radius:10px;padding:20px 22px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
            <span style="font-size:16px;">🤖</span>
            <p style="font-size:11px;font-weight:600;color:#7a95c8;letter-spacing:1px;text-transform:uppercase;margin:0;">AI Action Items</p>
        </div>
        <div style="display:flex;flex-direction:column;gap:10px;">
            @foreach ($actions as $i => $action)
                <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;background:rgba(122,149,200,0.06);border-radius:6px;border:1px solid rgba(122,149,200,0.1);">
                    <span style="font-size:12px;font-weight:700;color:#7a95c8;flex-shrink:0;margin-top:1px;">{{ $i+1 }}.</span>
                    <p style="font-size:12px;color:#a8a39c;margin:0;line-height:1.6;">{!! $action !!}</p>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
