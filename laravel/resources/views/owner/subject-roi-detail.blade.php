@extends('layouts.owner')
@section('title', 'Subject ROI · '.$subject->name)
@section('breadcrumb', 'Subject ROI · '.$subject->name)

@section('content')
<div style="max-width:1060px;">

    {{-- Back --}}
    <a href="{{ route('owner.subject-roi') }}" style="font-size:12px;color:#6a665f;text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:18px;">← Back to Subject ROI</a>

    {{-- Header --}}
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">{{ $subject->name }} — Teacher Comparison</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Side-by-side ROI and academic performance</p>
    </div>

    {{-- Institute summary banner --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:20px;">
        @foreach([
            ['Institute Avg', $instClassAvg.'%', '#7a95c8'],
            ['Strong Topics', $instStrongCount, '#7fb685'],
            ['Weak Topics',   $instWeakCount,   '#c87064'],
            ['At-Risk Students', $atRiskCount,  '#d4a574'],
        ] as [$lbl,$val,$col])
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:14px 16px;text-align:center;">
            <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;margin:0 0 4px;">{{ $lbl }}</p>
            <p style="font-size:22px;font-weight:800;color:{{ $col }};margin:0;letter-spacing:-0.4px;">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    @if(count($compareTeachers) >= 1)
    {{-- Teacher cards --}}
    <div style="display:grid;grid-template-columns:repeat({{ min(2,count($compareTeachers)) }},1fr);gap:16px;margin-bottom:20px;">
        @foreach($compareTeachers as $i => $td)
        @php
            $colors  = ['#a392c8','#7a95c8'];
            $tc      = $colors[$i] ?? '#a8a39c';
            $mc      = $td['classAvg'] >= 60 ? '#7fb685' : ($td['classAvg'] >= 40 ? '#d4a574' : '#c87064');
            $roiColor= $td['roi'] >= 5 ? '#7fb685' : ($td['roi'] >= 2.5 ? '#d4a574' : '#c87064');
        @endphp
        <div style="background:#14141b;border:1px solid {{ $tc }}40;border-top:2px solid {{ $tc }};border-radius:10px;padding:22px 24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:40px;height:40px;border-radius:8px;background:{{ $tc }}22;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:800;color:{{ $tc }};">
                        {{ strtoupper(substr($td['teacher']->name,0,1)) }}{{ strtoupper(substr(explode(' ',$td['teacher']->name)[1]??'X',0,1)) }}
                    </div>
                    <div>
                        <p style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0;">{{ $td['teacher']->name }}</p>
                        <p style="font-size:10px;color:#6a665f;margin:0;">{{ $td['studentCount'] }} students · {{ $td['batches']->count() }} batches</p>
                    </div>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:20px;font-weight:800;color:{{ $roiColor }};margin:0;">{{ $td['roi'] }}x</p>
                    <p style="font-size:9px;color:#6a665f;margin:0;">ROI</p>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px;">
                <div style="background:#0f0f14;border-radius:6px;padding:9px 10px;">
                    <p style="font-size:8px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">Annual Revenue</p>
                    <p style="font-size:14px;font-weight:700;color:#7fb685;margin:0;">₹{{ number_format($td['annualRevenue']) }}</p>
                </div>
                <div style="background:#0f0f14;border-radius:6px;padding:9px 10px;">
                    <p style="font-size:8px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">Annual Salary</p>
                    <p style="font-size:14px;font-weight:700;color:#d4a574;margin:0;">₹{{ number_format($td['annualSalary']) }}</p>
                </div>
                <div style="background:#0f0f14;border-radius:6px;padding:9px 10px;">
                    <p style="font-size:8px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">Net Contribution</p>
                    <p style="font-size:14px;font-weight:700;color:{{ $td['netContribution'] >= 0 ? '#7fb685' : '#c87064' }};margin:0;">₹{{ number_format($td['netContribution']) }}</p>
                </div>
                <div style="background:#0f0f14;border-radius:6px;padding:9px 10px;">
                    <p style="font-size:8px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">Effectiveness</p>
                    <p style="font-size:14px;font-weight:700;color:{{ $mc }};margin:0;">{{ $td['effectScore'] }}/100</p>
                </div>
            </div>

            <div style="margin-bottom:4px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:10px;color:#6a665f;">Class Average</span>
                    <span style="font-size:11px;font-weight:600;color:{{ $mc }};">{{ $td['classAvg'] }}%</span>
                </div>
                <div style="height:5px;background:rgba(245,241,232,0.06);border-radius:3px;overflow:hidden;">
                    <div style="height:100%;background:{{ $mc }};width:{{ $td['classAvg'] }}%;"></div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Decision chips --}}
    @if(!empty($chips))
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:20px;">
        @foreach($chips as $chip)
        <div style="background:rgba(245,241,232,0.04);border:1px solid rgba(245,241,232,0.08);border-radius:20px;padding:6px 14px;display:flex;align-items:center;gap:6px;">
            <span style="font-size:9px;color:{{ $chip['color'] }};font-weight:700;text-transform:uppercase;letter-spacing:0.9px;">{{ $chip['label'] }}</span>
            <span style="font-size:11px;color:#d4cfc8;">{{ $chip['value'] }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Topic comparison table --}}
    @if(!empty($topicRows))
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0;">Topic-wise Comparison</h2>
            <div style="display:flex;gap:6px;" id="topicFilterBtns">
                <button onclick="filterTopics('all')" data-filter="all" style="font-size:11px;padding:4px 10px;border-radius:4px;border:1px solid rgba(245,241,232,0.15);background:rgba(163,146,200,0.2);color:#a392c8;cursor:pointer;" class="active-filter">All</button>
                <button onclick="filterTopics('gap')" data-filter="gap" style="font-size:11px;padding:4px 10px;border-radius:4px;border:1px solid rgba(245,241,232,0.08);background:transparent;color:#6a665f;cursor:pointer;">Big Gaps</button>
                <button onclick="filterTopics('t0win')" data-filter="t0win" style="font-size:11px;padding:4px 10px;border-radius:4px;border:1px solid rgba(245,241,232,0.08);background:transparent;color:#6a665f;cursor:pointer;">{{ $compareTeachers[0]['teacher']->name ?? 'T1' }} wins</button>
                @if(isset($compareTeachers[1]))
                <button onclick="filterTopics('t1win')" data-filter="t1win" style="font-size:11px;padding:4px 10px;border-radius:4px;border:1px solid rgba(245,241,232,0.08);background:transparent;color:#6a665f;cursor:pointer;">{{ $compareTeachers[1]['teacher']->name ?? 'T2' }} wins</button>
                @endif
            </div>
        </div>

        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(245,241,232,0.08);">
                        <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;padding:0 0 10px;font-weight:700;">Topic</th>
                        @foreach($compareTeachers as $td)
                        <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;padding:0 10px 10px;font-weight:700;">{{ explode(' ', $td['teacher']->name)[0] }}</th>
                        @endforeach
                        <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;padding:0 10px 10px;font-weight:700;">Inst Avg</th>
                        <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;padding:0 0 10px;font-weight:700;">Gap</th>
                        <th style="text-align:center;font-size:9px;color:#a392c8;text-transform:uppercase;letter-spacing:0.9px;padding:0 0 10px;font-weight:700;">Owner Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topicRows as $row)
                    @php
                        $isGap = $row['gap'] !== null && abs($row['gap']) >= 15;
                        $isT0  = isset($row['winner']) && $row['winner'] === 0;
                        $isT1  = isset($row['winner']) && $row['winner'] === 1;
                        $gapColor = $row['gap'] === null ? '#6a665f' : (abs($row['gap']) < 3 ? '#6a665f' : ($row['gap'] > 0 ? '#7fb685' : '#c87064'));
                        $action = $isGap ? 'Align strategies' : ($row['gap'] === null ? '—' : 'Monitor');
                        $actionColor = $isGap ? '#d4a574' : '#6a665f';
                    @endphp
                    <tr data-gap="{{ $isGap ? 'true':'false' }}" data-t0="{{ $isT0 ? 'true':'false' }}" data-t1="{{ $isT1 ? 'true':'false' }}"
                        style="border-bottom:1px solid rgba(245,241,232,0.04);">
                        <td style="padding:9px 0;font-size:12px;color:#d4cfc8;">
                            {{ $row['node']->name }}
                            <span style="font-size:9px;color:#4a4740;margin-left:4px;">{{ $row['node']->code }}</span>
                        </td>
                        @foreach($row['values'] as $j => $val)
                        @php $vc = $val >= 60 ? '#7fb685' : ($val >= 40 ? '#d4a574' : '#c87064'); @endphp
                        <td style="text-align:center;padding:9px 10px;font-size:13px;font-weight:600;color:{{ $vc }};">{{ $val > 0 ? round($val,1).'%' : '—' }}</td>
                        @endforeach
                        <td style="text-align:center;padding:9px 10px;font-size:12px;color:#6a665f;">{{ $row['instAvg'] > 0 ? $row['instAvg'].'%' : '—' }}</td>
                        <td style="text-align:center;padding:9px 0;font-size:12px;font-weight:600;color:{{ $gapColor }};">
                            {{ $row['gap'] !== null ? ($row['gap'] > 0 ? '+' : '').$row['gap'].'pp' : '—' }}
                        </td>
                        <td style="text-align:center;padding:9px 0;">
                            <span style="font-size:10px;color:{{ $actionColor }};background:{{ $actionColor }}18;border-radius:4px;padding:3px 8px;">{{ $action }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- AI recommendations --}}
    @if(!empty($aiRecs))
    <div style="background:rgba(163,146,200,0.06);border:1px solid rgba(163,146,200,0.15);border-radius:10px;padding:20px 24px;">
        <p style="font-size:10px;font-weight:700;color:#a392c8;text-transform:uppercase;letter-spacing:1.2px;margin:0 0 12px;">Strategic Recommendations</p>
        @foreach($aiRecs as $rec)
        <div style="display:flex;gap:10px;margin-bottom:10px;align-items:flex-start;">
            <div style="width:4px;height:4px;border-radius:50%;background:#a392c8;margin-top:6px;flex-shrink:0;"></div>
            <p style="font-size:13px;color:#d4cfc8;margin:0;line-height:1.6;">{!! $rec !!}</p>
        </div>
        @endforeach
    </div>
    @endif
    @else
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:40px;text-align:center;">
        <p style="color:#6a665f;font-size:14px;">No teachers assigned to {{ $subject->name }} yet.</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
function filterTopics(filter) {
    document.querySelectorAll('tbody tr').forEach(function(row) {
        var show = true;
        if (filter === 'gap')   show = row.dataset.gap  === 'true';
        if (filter === 't0win') show = row.dataset.t0   === 'true';
        if (filter === 't1win') show = row.dataset.t1   === 'true';
        row.style.display = show ? '' : 'none';
    });
    document.querySelectorAll('#topicFilterBtns button').forEach(function(btn) {
        var active = btn.dataset.filter === filter;
        btn.style.background   = active ? 'rgba(163,146,200,0.2)' : 'transparent';
        btn.style.color        = active ? '#a392c8' : '#6a665f';
        btn.style.borderColor  = active ? 'rgba(163,146,200,0.3)' : 'rgba(245,241,232,0.08)';
    });
}
</script>
@endpush
@endsection
