@extends('layouts.academic-head')
@section('title', $subject->name . ' — Teacher Comparison')
@section('breadcrumb', 'Subject Comparison')

@section('content')
@php
    $subjectColors = ['P'=>'#7a95c8','C'=>'#d4a574','B'=>'#7fb685','Z'=>'#c87064','M'=>'#a8a39c'];
    $sColor        = $subjectColors[$subject->code] ?? '#a8a39c';
    $tCount        = count($compareTeachers);
    $totalStudents = collect($compareTeachers)->sum('studentCount');
@endphp

<div style="max-width:1060px;">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;font-size:13px;color:#6a665f;">
        <a href="{{ route('academic-head.subject-performance') }}" style="color:#6a665f;text-decoration:none;">Subject Performance</a>
        <span>›</span>
        <span style="color:#a8a39c;">{{ $subject->name }} Comparison</span>
    </div>

    {{-- 1. HEADER ─────────────────────────────────────────────────────────── --}}
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;">
        <div style="width:52px;height:52px;border-radius:10px;background:rgba({{ implode(',',sscanf($sColor,'#%02x%02x%02x')) }},0.15);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:800;color:{{ $sColor }};flex-shrink:0;">
            {{ $subject->code }}
        </div>
        <div>
            <h1 style="font-size:24px;font-weight:700;color:#f5f1e8;letter-spacing:-0.48px;margin:0 0 4px;">{{ $subject->name }} — Teacher Comparison</h1>
            <p style="font-size:13px;color:#a8a39c;margin:0;">{{ $tCount }} teacher{{ $tCount !== 1 ? 's' : '' }} · {{ $totalStudents }} students · Topic-wise side-by-side analysis</p>
        </div>
    </div>

    {{-- 2. SUBJECT OVERALL BANNER ─────────────────────────────────────────── --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:16px 22px;margin-bottom:18px;display:flex;gap:0;align-items:center;justify-content:space-around;flex-wrap:wrap;">
        @foreach([
            ['Class Average',   $instClassAvg.'%',  $instClassAvg >= 70 ? '#7fb685' : ($instClassAvg >= 40 ? '#d4a574' : '#c87064')],
            ['Strong Topics',   $instStrongCount,   '#7fb685'],
            ['Weak Topics',     $instWeakCount,     '#c87064'],
            ['At-Risk Students',$atRiskCount,        '#c87064'],
            ['Topics in Syllabus', $nodeIds->count(), '#a8a39c'],
        ] as [$label, $val, $color])
            <div style="text-align:center;padding:8px 18px;">
                <p style="font-size:22px;font-weight:700;color:{{ $color }};letter-spacing:-0.44px;margin:0 0 2px;">{{ $val }}</p>
                <p style="font-size:10px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.8px;">{{ $label }}</p>
            </div>
            @if (!$loop->last) <div style="width:1px;height:36px;background:rgba(245,241,232,0.06);"></div> @endif
        @endforeach
    </div>

    @if ($tCount === 0)
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:64px;text-align:center;">
            <p style="font-size:14px;color:#6a665f;margin:0;">No teachers assigned to {{ $subject->name }} yet.</p>
        </div>

    @elseif ($tCount === 1)
        {{-- Single teacher view --}}
        @php $td = $compareTeachers[0]; @endphp
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;margin-bottom:18px;display:flex;align-items:center;gap:20px;">
            <div style="width:52px;height:52px;border-radius:50%;background:rgba(122,149,200,0.15);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#7a95c8;flex-shrink:0;">
                {{ strtoupper(substr($td['teacher']->name,0,1)) }}
            </div>
            <div style="flex:1;">
                <h2 style="font-size:17px;font-weight:700;color:#f5f1e8;margin:0 0 4px;">{{ $td['teacher']->name }}</h2>
                <p style="font-size:12px;color:#6a665f;margin:0;">{{ $td['studentCount'] }} students · {{ $td['batches']->count() }} batches · Class Avg: {{ $td['classAvg'] }}%</p>
            </div>
            <a href="{{ route('academic-head.teacher-deep-dive', $td['teacher']->id) }}"
               style="font-size:12px;font-weight:600;color:#7a95c8;text-decoration:none;background:rgba(122,149,200,0.1);border-radius:4px;padding:8px 14px;">
                View Full Insights →
            </a>
        </div>
        <div style="background:rgba(212,165,116,0.06);border:1px solid rgba(212,165,116,0.2);border-radius:8px;padding:14px 18px;">
            <p style="font-size:13px;color:#d4a574;margin:0;">Only one teacher is assigned to {{ $subject->name }}. Assign another teacher to enable side-by-side comparison.</p>
        </div>

    @else
        {{-- 3. SIDE-BY-SIDE TEACHER CARDS ─────────────────────────────────── --}}
        <div style="display:grid;grid-template-columns:repeat({{ min($tCount,2) }},1fr);gap:16px;margin-bottom:16px;">
            @foreach (array_slice($compareTeachers,0,2) as $i => $td)
                @php
                    $mc     = $td['classAvg'] >= 70 ? '#7fb685' : ($td['classAvg'] >= 40 ? '#d4a574' : '#c87064');
                    $isWinner = $i === 0;
                @endphp
                <div style="background:#14141b;border:1px solid {{ $isWinner ? 'rgba(127,182,133,0.3)' : 'rgba(245,241,232,0.08)' }};border-radius:10px;padding:22px 24px;position:relative;">

                    @if ($isWinner)
                        <div style="position:absolute;top:12px;right:12px;background:rgba(127,182,133,0.15);border:1px solid rgba(127,182,133,0.3);border-radius:9999px;padding:3px 10px;font-size:10px;font-weight:700;color:#7fb685;">
                            🏆 WINNER
                        </div>
                    @endif

                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
                        <div style="width:48px;height:48px;border-radius:50%;background:rgba(122,149,200,0.15);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#7a95c8;flex-shrink:0;">
                            {{ strtoupper(substr($td['teacher']->name,0,1)) }}
                        </div>
                        <div>
                            <p style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0;">{{ $td['teacher']->name }}</p>
                            <p style="font-size:11px;color:#6a665f;margin:0;">{{ $td['batches']->count() }} batches · {{ $td['studentCount'] }} students</p>
                        </div>
                    </div>

                    {{-- Big metric --}}
                    <div style="margin-bottom:16px;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                            <span style="font-size:11px;color:#6a665f;">Class Average Mastery</span>
                            <span style="font-size:18px;font-weight:800;color:{{ $mc }};">{{ $td['classAvg'] }}%</span>
                        </div>
                        <div style="height:8px;background:rgba(245,241,232,0.06);border-radius:4px;overflow:hidden;">
                            <div style="height:100%;background:{{ $mc }};width:{{ $td['classAvg'] }}%;"></div>
                        </div>
                    </div>

                    {{-- Quick stats --}}
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:14px;">
                        @foreach([
                            ['Students',    $td['studentCount'], '#a8a39c'],
                            ['Batches',     $td['batches']->count(), '#a8a39c'],
                            ['Strong',      $td['strongCount'],  '#7fb685'],
                            ['Weak',        $td['weakCount'],    '#c87064'],
                        ] as [$lbl,$val,$color])
                            <div style="background:#0f0f14;border-radius:6px;padding:8px;text-align:center;">
                                <p style="font-size:16px;font-weight:700;color:{{ $color }};margin:0 0 2px;">{{ $val }}</p>
                                <p style="font-size:9px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.6px;">{{ $lbl }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            <span style="font-size:11px;color:#6a665f;">Effectiveness:</span>
                            <span style="font-size:13px;font-weight:700;color:{{ $td['effectScore'] >= 70 ? '#7fb685' : ($td['effectScore'] >= 45 ? '#d4a574' : '#c87064') }};">{{ $td['effectScore'] }}/100</span>
                        </div>
                        <a href="{{ route('academic-head.teacher-deep-dive', $td['teacher']->id) }}"
                           style="font-size:11px;font-weight:600;color:#7a95c8;text-decoration:none;background:rgba(122,149,200,0.1);border-radius:4px;padding:4px 8px;">
                            Deep Dive →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Other teachers note --}}
        @if (!empty($otherTeachers))
            <div style="background:rgba(122,149,200,0.06);border:1px solid rgba(122,149,200,0.15);border-radius:6px;padding:10px 14px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                <span style="font-size:12px;color:#7a95c8;">{{ count($otherTeachers) }} more teacher{{ count($otherTeachers) > 1 ? 's' : '' }} assigned to this subject:</span>
                @foreach ($otherTeachers as $ot)
                    <a href="{{ route('academic-head.teacher-deep-dive', $ot['teacher']->id) }}"
                       style="font-size:11px;font-weight:600;color:#7a95c8;text-decoration:none;background:rgba(122,149,200,0.1);border-radius:4px;padding:3px 8px;">{{ $ot['teacher']->name }} →</a>
                @endforeach
            </div>
        @endif

        {{-- 4. COMPARISON SUMMARY CHIPS ───────────────────────────────────── --}}
        @if (!empty($chips))
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-bottom:18px;">
                @foreach ($chips as $chip)
                    <div style="background:rgba({{ implode(',',sscanf($chip['color'],'#%02x%02x%02x')) }},0.07);border:1px solid {{ $chip['color'] }}28;border-radius:8px;padding:12px 16px;">
                        <p style="font-size:10px;font-weight:600;color:{{ $chip['color'] }};letter-spacing:0.88px;text-transform:uppercase;margin:0 0 3px;">{{ $chip['label'] }}</p>
                        <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">{{ $chip['value'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- 5. TOPIC-WISE COMPARISON TABLE ────────────────────────────────── --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;margin-bottom:18px;">

            {{-- Filter bar --}}
            <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);padding:12px 20px;display:flex;align-items:center;justify-content:space-between;">
                <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0;">Topic-wise Comparison ({{ $nodeIds->count() }} topics)</p>
                <div style="display:flex;gap:6px;" id="filterBtns">
                    @foreach([['all','All'],['biggap','Big Gaps (≥15pp)'],['bothstrong','Both Strong'],['bothweak','Both Weak']] as [$fv,$fl])
                        <button onclick="filterTopics('{{ $fv }}')"
                                id="btn-{{ $fv }}"
                                style="background:{{ $fv==='all'?'rgba(122,149,200,0.15)':'#0f0f14' }};border:1px solid {{ $fv==='all'?'rgba(122,149,200,0.3)':'rgba(245,241,232,0.08)' }};border-radius:4px;padding:4px 10px;font-size:11px;font-weight:500;color:{{ $fv==='all'?'#7a95c8':'#a8a39c' }};cursor:pointer;">
                            {{ $fl }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Column headers --}}
            @php
                $t0 = $compareTeachers[0];
                $t1 = $compareTeachers[1] ?? null;
            @endphp
            <div style="display:grid;grid-template-columns:90px 1fr {{ $t1 ? '160px 160px 70px 60px' : '1fr' }};gap:8px;padding:10px 20px;border-bottom:1px solid rgba(245,241,232,0.05);background:#0f0f14;">
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">CODE</span>
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">TOPIC</span>
                <span style="font-size:10px;font-weight:500;color:#7fb685;letter-spacing:1px;">{{ strtoupper(explode(' ', $t0['teacher']->name)[0]) }}</span>
                @if ($t1)
                    <span style="font-size:10px;font-weight:500;color:#7a95c8;letter-spacing:1px;">{{ strtoupper(explode(' ', $t1['teacher']->name)[0]) }}</span>
                    <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">GAP</span>
                    <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">WIN</span>
                @endif
            </div>

            <div id="topicTableBody" style="max-height:480px;overflow-y:auto;">
                @foreach ($topicRows as $row)
                    @php
                        $v0     = isset($row['values'][0]) ? (int)round($row['values'][0]) : null;
                        $v1     = isset($row['values'][1]) ? (int)round($row['values'][1]) : null;
                        $gap    = $row['gap'];
                        $winner = $row['winner'];
                        $c0     = $v0 !== null ? ($v0 >= 70 ? '#7fb685' : ($v0 >= 40 ? '#d4a574' : '#c87064')) : '#6a665f';
                        $c1     = $v1 !== null ? ($v1 >= 70 ? '#7fb685' : ($v1 >= 40 ? '#d4a574' : '#c87064')) : '#6a665f';
                        $isBigGap    = $gap !== null && abs($gap) >= 15;
                        $isBothStrong = $v0 !== null && $v1 !== null && $v0 >= 70 && $v1 >= 70;
                        $isBothWeak  = $v0 !== null && $v1 !== null && $v0 < 40 && $v1 < 40;
                        $dataFilter  = 'all' . ($isBigGap ? ' biggap' : '') . ($isBothStrong ? ' bothstrong' : '') . ($isBothWeak ? ' bothweak' : '');
                    @endphp
                    <div class="topic-row" data-filter="{{ $dataFilter }}"
                         style="display:grid;grid-template-columns:90px 1fr {{ $t1 ? '160px 160px 70px 60px' : '1fr' }};gap:8px;padding:9px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.03);"
                         onmouseover="this.style.background='rgba(26,26,36,0.4)'" onmouseout="this.style.background=''">

                        <span style="font-size:10px;font-weight:600;color:#6a665f;font-family:monospace;">{{ $row['node']->code }}</span>
                        <span style="font-size:12px;color:#f5f1e8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $row['node']->name }}</span>

                        {{-- Teacher 0 bar --}}
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="flex:1;height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                                <div style="height:100%;background:{{ $c0 }};width:{{ $v0 ?? 0 }}%;"></div>
                            </div>
                            <span style="font-size:11px;font-weight:700;color:{{ $c0 }};min-width:28px;text-align:right;">{{ $v0 !== null ? $v0.'%' : '–' }}</span>
                        </div>

                        @if ($t1)
                        {{-- Teacher 1 bar --}}
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="flex:1;height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                                <div style="height:100%;background:{{ $c1 }};width:{{ $v1 ?? 0 }}%;"></div>
                            </div>
                            <span style="font-size:11px;font-weight:700;color:{{ $c1 }};min-width:28px;text-align:right;">{{ $v1 !== null ? $v1.'%' : '–' }}</span>
                        </div>

                        {{-- Gap --}}
                        <span style="font-size:11px;font-weight:600;color:{{ $gap !== null ? ($gap > 0 ? '#7fb685' : '#c87064') : '#6a665f' }};text-align:center;">
                            {{ $gap !== null ? ($gap > 0 ? '+' : '') . $gap . 'pp' : '–' }}
                        </span>

                        {{-- Winner --}}
                        <span style="font-size:10px;font-weight:700;text-align:center;">
                            @if ($winner === 0)
                                <span style="color:#7fb685;">W1</span>
                            @elseif ($winner === 1)
                                <span style="color:#7a95c8;">W2</span>
                            @elseif ($winner === 'tie')
                                <span style="color:#6a665f;">TIE</span>
                            @else
                                <span style="color:#6a665f;">–</span>
                            @endif
                        </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- 6. AI CROSS-TEACHER RECOMMENDATIONS ───────────────────────────── --}}
        @if (!empty($aiRecs))
        <div style="background:rgba(122,149,200,0.06);border:1px solid rgba(122,149,200,0.2);border-radius:10px;padding:20px 22px;">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                <span style="font-size:16px;">🤖</span>
                <p style="font-size:11px;font-weight:600;color:#7a95c8;letter-spacing:1px;text-transform:uppercase;margin:0;">AI Cross-Teacher Recommendations</p>
            </div>
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach ($aiRecs as $i => $rec)
                    <div style="display:flex;gap:10px;padding:10px 14px;background:rgba(122,149,200,0.05);border-radius:6px;border:1px solid rgba(122,149,200,0.1);">
                        <span style="font-size:12px;font-weight:700;color:#7a95c8;flex-shrink:0;margin-top:1px;">{{ $i+1 }}.</span>
                        <p style="font-size:12px;color:#a8a39c;margin:0;line-height:1.6;">{!! $rec !!}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

    @endif
</div>

@push('scripts')
<script>
function filterTopics(filter) {
    // Update button styles
    ['all','biggap','bothstrong','bothweak'].forEach(f => {
        const btn = document.getElementById('btn-' + f);
        if (f === filter) {
            btn.style.background = 'rgba(122,149,200,0.15)';
            btn.style.borderColor = 'rgba(122,149,200,0.3)';
            btn.style.color = '#7a95c8';
        } else {
            btn.style.background = '#0f0f14';
            btn.style.borderColor = 'rgba(245,241,232,0.08)';
            btn.style.color = '#a8a39c';
        }
    });
    // Show/hide rows
    document.querySelectorAll('.topic-row').forEach(row => {
        const filters = row.getAttribute('data-filter').split(' ');
        row.style.display = (filter === 'all' || filters.includes(filter)) ? 'grid' : 'none';
    });
}
</script>
@endpush
@endsection
