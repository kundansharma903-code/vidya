@extends('layouts.owner')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
@php
    $hsColor = $healthScore >= 75 ? '#7fb685' : ($healthScore >= 50 ? '#d4a574' : '#c87064');
    $circ    = 339.3;
    $hsOff   = $circ - ($healthScore / 100 * $circ);
@endphp
<div style="max-width:1100px;">

    {{-- Page header --}}
    <div style="margin-bottom:24px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Business Overview</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">{{ now()->format('l, d F Y') }} · ABC Coaching Institute</p>
    </div>

    {{-- Top row: Health Score + Revenue KPIs --}}
    <div style="display:grid;grid-template-columns:260px 1fr;gap:16px;margin-bottom:16px;">

        {{-- Business Health Score --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;display:flex;flex-direction:column;align-items:center;">
            <p style="font-size:10px;font-weight:700;color:#6a665f;text-transform:uppercase;letter-spacing:1.2px;margin:0 0 14px;">Business Health</p>
            <svg width="120" height="120" viewBox="0 0 120 120" style="margin-bottom:10px;">
                <circle cx="60" cy="60" r="54" fill="none" stroke="rgba(245,241,232,0.06)" stroke-width="10"/>
                <circle cx="60" cy="60" r="54" fill="none" stroke="{{ $hsColor }}" stroke-width="10"
                    stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $hsOff }}"
                    stroke-linecap="round" transform="rotate(-90 60 60)"/>
                <text x="60" y="55" text-anchor="middle" font-size="26" font-weight="800" fill="{{ $hsColor }}" font-family="-apple-system,sans-serif">{{ $healthScore }}</text>
                <text x="60" y="70" text-anchor="middle" font-size="10" fill="#6a665f" font-family="-apple-system,sans-serif">/ 100</text>
            </svg>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;width:100%;">
                @foreach([['Finance', $financeHealth,'#a392c8'],['Academic',$academicHealth,'#7a95c8'],['Retention',$retentionRate,'#7fb685'],['Coverage',$curriculumPct,'#d4a574']] as [$lbl,$val,$col])
                <div style="background:#0f0f14;border-radius:6px;padding:7px 8px;text-align:center;">
                    <p style="font-size:8px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">{{ $lbl }}</p>
                    <p style="font-size:14px;font-weight:700;color:{{ $col }};margin:0;">{{ $val }}%</p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Financial KPIs --}}
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            @php
                $kpis = [
                    ['Monthly Revenue', '₹'.number_format($monthlyRevenue), '+'.count($teacherRoi).' teachers', '#7fb685'],
                    ['Annual Revenue',  '₹'.number_format($annualRevenue),  $totalStudents.' students enrolled', '#a392c8'],
                    ['Annual Cost',     '₹'.number_format($annualCost),     'Staff salaries total', '#d4a574'],
                    ['Net Profit',      '₹'.number_format($annualProfit),   $margin.'% margin · '.($margin >= 35 ? 'Healthy' : ($margin >= 20 ? 'Moderate' : 'Low')), $margin >= 35 ? '#7fb685' : ($margin >= 20 ? '#d4a574' : '#c87064')],
                ];
            @endphp
            @foreach($kpis as [$label,$value,$sub,$color])
            <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
                <p style="font-size:10px;font-weight:700;color:#6a665f;text-transform:uppercase;letter-spacing:1.1px;margin:0 0 8px;">{{ $label }}</p>
                <p style="font-size:26px;font-weight:800;color:{{ $color }};letter-spacing:-0.8px;margin:0 0 4px;">{{ $value }}</p>
                <p style="font-size:11px;color:#4a4740;margin:0;">{{ $sub }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Second row: Teacher ROI + Alerts --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">

        {{-- Teacher ROI Summary --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0;">Teacher ROI</h2>
                <a href="{{ route('owner.teachers') }}" style="font-size:11px;color:#a392c8;text-decoration:none;">View all →</a>
            </div>
            @foreach($teacherRoi as $tr)
            @php
                $roiColor = $tr['roi'] >= 4 ? '#7fb685' : ($tr['roi'] >= 2 ? '#d4a574' : '#c87064');
            @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid rgba(245,241,232,0.05);">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:6px;background:rgba(163,146,200,0.12);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#a392c8;">
                        {{ strtoupper(substr($tr['teacher']->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $tr['teacher']->name)[1] ?? 'X', 0, 1)) }}
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">{{ $tr['teacher']->name }}</p>
                        <p style="font-size:11px;color:#6a665f;margin:0;">{{ $tr['students'] }} students · ₹{{ number_format(($tr['teacher']->monthly_salary ?? 0)) }}/mo</p>
                    </div>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:18px;font-weight:800;color:{{ $roiColor }};margin:0;letter-spacing:-0.4px;">{{ $tr['roi'] }}x</p>
                    <p style="font-size:10px;color:#6a665f;margin:0;">₹{{ number_format($tr['revenue']) }} rev</p>
                </div>
            </div>
            @endforeach
            @if(empty($teacherRoi))
            <p style="font-size:12px;color:#4a4740;text-align:center;padding:20px 0;">No teachers assigned yet.</p>
            @endif
        </div>

        {{-- Strategic Alerts --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0;">Strategic Alerts</h2>
                <a href="{{ route('owner.strategic-alerts') }}" style="font-size:11px;color:#a392c8;text-decoration:none;">View all →</a>
            </div>
            @foreach($alerts as $alert)
            @php
                $alertColors = ['critical'=>['#c87064','rgba(200,112,100,0.1)'],'warning'=>['#d4a574','rgba(212,165,116,0.08)'],'success'=>['#7fb685','rgba(127,182,133,0.08)']];
                [$ac,$abg] = $alertColors[$alert['level']] ?? ['#a8a39c','rgba(168,163,156,0.08)'];
            @endphp
            <div style="background:{{ $abg }};border-left:2px solid {{ $ac }};border-radius:0 6px 6px 0;padding:10px 12px;margin-bottom:8px;">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:3px;">
                    <span style="font-size:9px;font-weight:700;color:{{ $ac }};text-transform:uppercase;letter-spacing:0.9px;">{{ $alert['category'] }}</span>
                </div>
                <p style="font-size:12px;color:#d4cfc8;margin:0;line-height:1.5;">{{ $alert['msg'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Third row: Academic snapshot + Quick actions --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        {{-- Academic Snapshot --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">
            <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0 0 16px;">Academic Snapshot</h2>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px;">
                @php
                    $aKpis = [
                        ['Class Avg', $classAvg.'%', $classAvg >= 60 ? '#7fb685' : ($classAvg >= 40 ? '#d4a574' : '#c87064')],
                        ['Coverage', $curriculumPct.'%', '#7a95c8'],
                        ['Retention', $retentionRate.'%', '#7fb685'],
                    ];
                @endphp
                @foreach($aKpis as [$lbl,$val,$col])
                <div style="background:#0f0f14;border-radius:6px;padding:10px 12px;text-align:center;">
                    <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;margin:0 0 3px;">{{ $lbl }}</p>
                    <p style="font-size:20px;font-weight:700;color:{{ $col }};margin:0;letter-spacing:-0.4px;">{{ $val }}</p>
                </div>
                @endforeach
            </div>
            @if($recentTests->isNotEmpty())
            <p style="font-size:10px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;margin:0 0 8px;">Recent Tests</p>
            @foreach($recentTests as $t)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                <span style="font-size:12px;color:#a8a39c;">{{ $t->name }}</span>
                <span style="font-size:11px;color:#6a665f;">{{ \Carbon\Carbon::parse($t->test_date)->format('d M') }}</span>
            </div>
            @endforeach
            @else
            <p style="font-size:12px;color:#4a4740;padding:10px 0;">No analyzed tests yet.</p>
            @endif
        </div>

        {{-- Quick Actions --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">
            <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0 0 16px;">Quick Actions</h2>
            @php
                $actions = [
                    ['Review Teacher Performance', route('owner.teachers'),          '#a392c8', 'Rank all teachers by ROI'],
                    ['View Financial Summary',      route('owner.financial'),         '#7fb685', 'P&L, revenue trends'],
                    ['Check Strategic Alerts',      route('owner.strategic-alerts'),  '#c87064', count($alerts).' active alerts'],
                    ['Staff Decisions',             route('owner.staff-decisions'),   '#d4a574', 'Pending decisions'],
                    ['Subject ROI Analysis',        route('owner.subject-roi'),       '#7a95c8', 'Per-subject business view'],
                    ['At-Risk Students',            route('owner.at-risk-students'),  '#c87064', $totalStudents.' total enrolled'],
                ];
            @endphp
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                @foreach($actions as [$label,$href,$color,$sub])
                <a href="{{ $href }}" style="background:#0f0f14;border:1px solid rgba(245,241,232,0.06);border-radius:8px;padding:12px 14px;text-decoration:none;display:block;transition:border-color 0.15s;" onmouseover="this.style.borderColor='{{ $color }}40'" onmouseout="this.style.borderColor='rgba(245,241,232,0.06)'">
                    <p style="font-size:12px;font-weight:600;color:#f5f1e8;margin:0 0 3px;">{{ $label }}</p>
                    <p style="font-size:10px;color:#6a665f;margin:0;">{{ $sub }}</p>
                    <div style="margin-top:8px;width:20px;height:2px;background:{{ $color }};border-radius:1px;opacity:0.6;"></div>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
