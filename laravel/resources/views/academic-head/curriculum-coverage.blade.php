@extends('layouts.academic-head')
@section('title', 'Curriculum Coverage')
@section('breadcrumb', 'Curriculum Coverage')

@section('content')
<div style="max-width:1060px;">

    {{-- Urgency banner --}}
    @if ($subjectsBehind > 0)
    <div style="background:rgba(200,112,100,0.08);border:1px solid rgba(200,112,100,0.25);border-radius:8px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:12px;">
        <span style="font-size:16px;">⚠️</span>
        <p style="font-size:13px;color:#f5f1e8;margin:0;"><strong style="color:#c87064;">{{ $weeksLeft }} weeks remaining</strong> · {{ $subjectsBehind }} {{ $subjectsBehind === 1 ? 'subject' : 'subjects' }} below 60% coverage — pace adjustment needed.</p>
    </div>
    @endif

    {{-- 4 KPIs --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;">
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Total Coverage</p>
            <p style="font-size:28px;font-weight:700;color:{{ $overallPct >= 70 ? '#7fb685' : ($overallPct >= 40 ? '#d4a574' : '#c87064') }};letter-spacing:-0.56px;margin:0;">{{ $overallPct }}%</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">{{ $coveredNodes }}/{{ $totalNodes }} topics</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.12);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Topics Pending</p>
            <p style="font-size:28px;font-weight:700;color:#c87064;letter-spacing:-0.56px;margin:0;">{{ $pending }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Not yet tested</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Weeks Remaining</p>
            <p style="font-size:28px;font-weight:700;color:#7a95c8;letter-spacing:-0.56px;margin:0;">{{ $weeksLeft }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">To NEET 2025</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Subjects Behind</p>
            <p style="font-size:28px;font-weight:700;color:{{ $subjectsBehind > 0 ? '#d4a574' : '#7fb685' }};letter-spacing:-0.56px;margin:0;">{{ $subjectsBehind }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Below 60% coverage</p>
        </div>
    </div>

    {{-- Subject coverage cards --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:14px;margin-bottom:20px;">
        @php $subjectColors = ['P'=>'#7a95c8','C'=>'#d4a574','B'=>'#7fb685','Z'=>'#c87064','M'=>'#a8a39c']; @endphp
        @foreach ($subjectData as $sp)
            @php
                $color = $subjectColors[$sp['code']] ?? '#a8a39c';
                $status = $sp['pct'] >= 80 ? ['On Track','#7fb685'] : ($sp['pct'] >= 50 ? ['In Progress','#d4a574'] : ['Behind','#c87064']);
                $recPace = $sp['uncovered'] > 0 && $weeksLeft > 0
                    ? ceil($sp['uncovered'] / $weeksLeft)
                    : 0;
            @endphp
            <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:42px;height:42px;border-radius:8px;background:rgba({{ implode(',',sscanf($color,'#%02x%02x%02x')) }},0.15);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:{{ $color }};">
                            {{ $sp['code'] }}
                        </div>
                        <div>
                            <p style="font-size:15px;font-weight:700;color:#f5f1e8;margin:0;">{{ $sp['name'] }}</p>
                            <span style="font-size:10px;font-weight:600;color:{{ $status[1] }};">● {{ $status[0] }}</span>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <p style="font-size:28px;font-weight:800;color:{{ $color }};margin:0;letter-spacing:-0.56px;">{{ $sp['pct'] }}%</p>
                        <p style="font-size:10px;color:#6a665f;margin:0;">{{ $sp['covered'] }}/{{ $sp['total'] }} topics</p>
                    </div>
                </div>

                <div style="height:6px;background:rgba(245,241,232,0.06);border-radius:3px;overflow:hidden;margin-bottom:12px;">
                    <div style="height:100%;background:{{ $color }};width:{{ $sp['pct'] }}%;"></div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div style="background:#0f0f14;border-radius:6px;padding:8px 10px;">
                        <p style="font-size:10px;color:#6a665f;margin:0 0 2px;">Weeks Left</p>
                        <p style="font-size:14px;font-weight:600;color:#a8a39c;margin:0;">{{ $weeksLeft }}w</p>
                    </div>
                    <div style="background:#0f0f14;border-radius:6px;padding:8px 10px;">
                        <p style="font-size:10px;color:#6a665f;margin:0 0 2px;">Rec. Pace</p>
                        <p style="font-size:14px;font-weight:600;color:#a8a39c;margin:0;">{{ $recPace }} topics/wk</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Critical Gaps --}}
    @php $allUncovered = collect($subjectData)->flatMap(fn($s) => $s['uncoveredNodes']->map(fn($n) => ['subject'=>$s['name'],'code'=>$s['code'],'node'=>$n]))->values(); @endphp
    @if ($allUncovered->isNotEmpty())
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.2);border-radius:10px;padding:20px 22px;">
            <p style="font-size:11px;font-weight:500;color:#c87064;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;">Critical Gaps — Topics Never Tested ({{ $allUncovered->count() }})</p>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                @foreach ($allUncovered->take(18) as $item)
                    <div style="display:flex;align-items:center;gap:8px;background:#0f0f14;border-radius:6px;padding:8px 10px;">
                        <span style="font-size:10px;font-weight:600;color:{{ $subjectColors[$item['code']] ?? '#a8a39c' }};font-family:monospace;flex-shrink:0;">{{ $item['code'] }}</span>
                        <span style="font-size:11px;color:#a8a39c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['node']->name }}</span>
                    </div>
                @endforeach
            </div>
            @if ($allUncovered->count() > 18)
                <p style="font-size:11px;color:#6a665f;margin:10px 0 0;">+ {{ $allUncovered->count() - 18 }} more topics not yet tested</p>
            @endif
        </div>
    @endif
</div>
@endsection
