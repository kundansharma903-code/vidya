@extends('layouts.owner')
@section('title', $teacher->name)
@section('breadcrumb', 'Teacher · '.$teacher->name)

@section('content')
@php
    $roiColor  = $roi >= 5 ? '#7fb685' : ($roi >= 2.5 ? '#d4a574' : '#c87064');
    $mc        = $classAvg >= 60 ? '#7fb685' : ($classAvg >= 40 ? '#d4a574' : '#c87064');
    $circ      = 339.3;
    $effOff    = $circ - ($effectScore / 100 * $circ);
    $effColor  = $effectScore >= 70 ? '#7fb685' : ($effectScore >= 45 ? '#d4a574' : '#c87064');
@endphp
<div style="max-width:1060px;">

    <a href="{{ route('owner.teachers') }}" style="font-size:12px;color:#6a665f;text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:18px;">← Back to Teachers</a>

    {{-- Profile header --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 26px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div style="width:54px;height:54px;border-radius:10px;background:rgba(163,146,200,0.15);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:#a392c8;">
                {{ strtoupper(substr($teacher->name,0,1)) }}{{ strtoupper(substr(explode(' ',$teacher->name)[1]??'X',0,1)) }}
            </div>
            <div>
                <h1 style="font-size:20px;font-weight:700;color:#f5f1e8;margin:0 0 3px;">{{ $teacher->name }}</h1>
                <p style="font-size:12px;color:#6a665f;margin:0;">{{ $subjectName }} · {{ $studentIds->count() }} students · {{ $batches->count() }} batches</p>
                @if($tenureYears !== null)
                <p style="font-size:11px;color:#4a4740;margin:2px 0 0;">{{ $tenureYears }} years tenure · Since {{ \Carbon\Carbon::parse($teacher->tenure_start)->format('M Y') }}</p>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <div style="text-align:right;background:rgba(163,146,200,0.08);border:1px solid rgba(163,146,200,0.2);border-radius:8px;padding:10px 16px;">
                <p style="font-size:9px;color:#a392c8;text-transform:uppercase;letter-spacing:1px;margin:0 0 2px;">Monthly Salary</p>
                <p style="font-size:18px;font-weight:800;color:#a392c8;margin:0;">₹{{ number_format($teacher->monthly_salary ?? 0) }}</p>
            </div>
            <div style="text-align:right;background:{{ $roiColor }}15;border:1px solid {{ $roiColor }}40;border-radius:8px;padding:10px 16px;">
                <p style="font-size:9px;color:{{ $roiColor }};text-transform:uppercase;letter-spacing:1px;margin:0 0 2px;">ROI · #{{ $roiRank }}/{{ $totalTeachers }}</p>
                <p style="font-size:18px;font-weight:800;color:{{ $roiColor }};margin:0;">{{ $roi }}x</p>
            </div>
        </div>
    </div>

    {{-- ROI banner --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px;">
        @foreach([
            ['Annual Revenue', '₹'.number_format($annualRevenue), '#7fb685'],
            ['Annual Salary',  '₹'.number_format($annualSalary),  '#d4a574'],
            ['Net Contribution','₹'.number_format($netContrib),    $netContrib >= 0 ? '#7fb685' : '#c87064'],
            ['Effectiveness',  $effectScore.'/100',                $effColor],
        ] as [$lbl,$val,$col])
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:14px 16px;text-align:center;">
            <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;margin:0 0 4px;">{{ $lbl }}</p>
            <p style="font-size:20px;font-weight:800;color:{{ $col }};margin:0;letter-spacing:-0.4px;">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    <div style="display:grid;grid-template-columns:220px 1fr;gap:16px;margin-bottom:16px;">
        {{-- Effectiveness gauge --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px;display:flex;flex-direction:column;align-items:center;">
            <p style="font-size:10px;font-weight:700;color:#6a665f;text-transform:uppercase;letter-spacing:1.1px;margin:0 0 12px;">Effectiveness</p>
            <svg width="120" height="120" viewBox="0 0 120 120">
                <circle cx="60" cy="60" r="54" fill="none" stroke="rgba(245,241,232,0.06)" stroke-width="10"/>
                <circle cx="60" cy="60" r="54" fill="none" stroke="{{ $effColor }}" stroke-width="10"
                    stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $effOff }}"
                    stroke-linecap="round" transform="rotate(-90 60 60)"/>
                <text x="60" y="55" text-anchor="middle" font-size="26" font-weight="800" fill="{{ $effColor }}" font-family="-apple-system,sans-serif">{{ $effectScore }}</text>
                <text x="60" y="70" text-anchor="middle" font-size="10" fill="#6a665f" font-family="-apple-system,sans-serif">/100</text>
            </svg>
            <div style="margin-top:10px;text-align:center;">
                <p style="font-size:12px;color:#d4cfc8;font-weight:600;margin:0;">{{ $effectScore >= 70 ? 'High Performer' : ($effectScore >= 45 ? 'Average' : 'Needs Support') }}</p>
                <p style="font-size:10px;color:#6a665f;margin:3px 0 0;">Rank {{ $roiRank }} / {{ $totalTeachers }}</p>
            </div>
        </div>

        {{-- KPIs --}}
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;align-content:start;">
            @foreach([
                ['Class Avg', $classAvg.'%', $mc],
                ['Weak Topics', $weakCount, '#c87064'],
                ['Strong Topics', $strongCount, '#7fb685'],
                ['At-Risk Students', $atRiskCount.' ('.$atRiskPct.'%)', $atRiskPct > 30 ? '#c87064' : '#d4a574'],
            ] as [$lbl,$val,$col])
            <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:14px 16px;">
                <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;margin:0 0 5px;">{{ $lbl }}</p>
                <p style="font-size:22px;font-weight:800;color:{{ $col }};margin:0;letter-spacing:-0.4px;">{{ $val }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Strong / Weak topics --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        @foreach([['Strong Topics', $strongTopics, '#7fb685', 'sortByDesc'], ['Weak Topics', $weakTopics, '#c87064', 'sortBy']] as [$title,$rows,$col,$_])
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
            <h2 style="font-size:13px;font-weight:700;color:{{ $col }};margin:0 0 12px;text-transform:uppercase;letter-spacing:0.8px;">{{ $title }}</h2>
            @forelse($rows as $t)
            @php $tc = $t->avg_m >= 60 ? '#7fb685' : ($t->avg_m >= 40 ? '#d4a574' : '#c87064'); @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                <div>
                    <p style="font-size:12px;color:#d4cfc8;margin:0;font-weight:500;">{{ $t->topic_name }}</p>
                    <p style="font-size:10px;color:#6a665f;margin:0;">{{ $t->topic_code }} · vs inst {{ $t->inst_avg }}%</p>
                </div>
                <div style="display:flex;align-items:center;gap:8px;">
                    @if($t->diff != 0)
                    <span style="font-size:10px;color:{{ $t->diff > 0 ? '#7fb685' : '#c87064' }};">{{ $t->diff > 0 ? '+' : '' }}{{ $t->diff }}pp</span>
                    @endif
                    <span style="font-size:14px;font-weight:700;color:{{ $tc }};">{{ round($t->avg_m, 1) }}%</span>
                </div>
            </div>
            @empty
            <p style="font-size:12px;color:#4a4740;padding:10px 0;">No data available.</p>
            @endforelse
        </div>
        @endforeach
    </div>

    {{-- Batch revenue breakdown --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;margin-bottom:16px;">
        <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0 0 14px;">Batch Revenue Breakdown</h2>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid rgba(245,241,232,0.08);">
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;padding:0 0 10px;font-weight:700;">Batch</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;padding:0 0 10px;font-weight:700;">Students</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;padding:0 0 10px;font-weight:700;">Monthly Fee</th>
                    <th style="text-align:right;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;padding:0 0 10px;font-weight:700;">Annual Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($batchRevenue as $br)
                <tr style="border-bottom:1px solid rgba(245,241,232,0.04);">
                    <td style="padding:10px 0;font-size:13px;color:#d4cfc8;">{{ $br['batch']->name }}</td>
                    <td style="text-align:center;padding:10px 0;font-size:13px;color:#a8a39c;">{{ $br['students'] }}</td>
                    <td style="text-align:center;padding:10px 0;font-size:13px;color:#d4a574;">₹{{ number_format($br['monthly_fee']) }}</td>
                    <td style="text-align:right;padding:10px 0;font-size:13px;font-weight:600;color:#7fb685;">₹{{ number_format($br['annual_revenue']) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" style="padding:10px 0;font-size:12px;color:#6a665f;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;">Total</td>
                    <td style="text-align:right;padding:10px 0;font-size:14px;font-weight:800;color:#7fb685;">₹{{ number_format($annualRevenue) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Test trend --}}
    @if(!empty($testTrend))
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;margin-bottom:16px;">
        <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0 0 16px;">Test Performance Trend</h2>
        <div style="display:flex;align-items:flex-end;gap:10px;height:80px;">
            @foreach($testTrend as $t)
            @php $h = max(4, $t['teacherPct']); $ih = max(4, $t['instPct']); $tc2 = $t['teacherPct'] >= 60 ? '#7fb685' : ($t['teacherPct'] >= 40 ? '#d4a574' : '#c87064'); @endphp
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;">
                <div style="width:100%;display:flex;gap:2px;align-items:flex-end;height:60px;">
                    <div style="flex:1;background:{{ $tc2 }};height:{{ $h }}%;border-radius:2px 2px 0 0;" title="{{ $t['code'] }}: {{ $t['teacherPct'] }}%"></div>
                    <div style="flex:1;background:rgba(245,241,232,0.15);height:{{ $ih }}%;border-radius:2px 2px 0 0;" title="Inst avg: {{ $t['instPct'] }}%"></div>
                </div>
                <p style="font-size:8px;color:#4a4740;text-align:center;margin:0;max-width:40px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $t['code'] }}</p>
            </div>
            @endforeach
        </div>
        <div style="display:flex;gap:14px;margin-top:10px;">
            <div style="display:flex;align-items:center;gap:5px;"><div style="width:10px;height:10px;background:#7fb685;border-radius:2px;"></div><span style="font-size:10px;color:#6a665f;">Teacher avg</span></div>
            <div style="display:flex;align-items:center;gap:5px;"><div style="width:10px;height:10px;background:rgba(245,241,232,0.15);border-radius:2px;"></div><span style="font-size:10px;color:#6a665f;">Institute avg</span></div>
        </div>
    </div>
    @endif

    {{-- Owner Decision Panel --}}
    <div style="background:rgba(163,146,200,0.06);border:1px solid rgba(163,146,200,0.2);border-radius:10px;padding:22px 24px;">
        <p style="font-size:10px;font-weight:700;color:#a392c8;text-transform:uppercase;letter-spacing:1.2px;margin:0 0 14px;">Owner Decision Panel</p>
        <div style="display:grid;grid-template-columns:repeat({{ count($decisions) }},1fr);gap:10px;">
            @foreach($decisions as $dec)
            <button onclick="showDecisionToast('{{ $dec['action'] }}')"
                style="background:{{ $dec['color'] }}15;border:1px solid {{ $dec['color'] }}40;border-radius:8px;padding:14px;text-align:left;cursor:pointer;transition:background 0.15s;"
                onmouseover="this.style.background='{{ $dec['color'] }}25'" onmouseout="this.style.background='{{ $dec['color'] }}15'">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                    <span style="font-size:16px;">{{ $dec['icon'] }}</span>
                    <span style="font-size:13px;font-weight:700;color:{{ $dec['color'] }};">{{ $dec['action'] }}</span>
                </div>
                <p style="font-size:11px;color:#a8a39c;margin:0;line-height:1.4;">{{ $dec['reason'] }}</p>
            </button>
            @endforeach
        </div>
    </div>

    {{-- Toast --}}
    <div id="decisionToast" style="display:none;position:fixed;bottom:24px;right:24px;background:#14141b;border:1px solid rgba(163,146,200,0.3);border-radius:8px;padding:14px 20px;z-index:9999;color:#f5f1e8;font-size:13px;box-shadow:0 8px 24px rgba(0,0,0,0.4);">
        Action logged: <strong id="toastAction"></strong>
    </div>
</div>

@push('scripts')
<script>
function showDecisionToast(action) {
    document.getElementById('toastAction').textContent = action;
    var toast = document.getElementById('decisionToast');
    toast.style.display = 'block';
    setTimeout(function() { toast.style.display = 'none'; }, 3000);
}
</script>
@endpush
@endsection
