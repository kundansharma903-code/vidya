@extends('layouts.owner')
@section('title', 'Student Rankings')
@section('breadcrumb', 'Student Rankings')

@section('content')
<div style="max-width:1040px;">

    <div style="margin-bottom:20px;">
        <h1 style="font-size:24px;font-weight:700;color:#f5f1e8;letter-spacing:-0.5px;margin:0 0 4px;">Student Rankings</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Institute-wide · all batches</p>
    </div>

    {{-- Test selector --}}
    <form method="GET" style="margin-bottom:20px;">
        <div style="display:flex;gap:10px;align-items:center;">
            <select name="test_id" onchange="this.form.submit()"
                style="flex:1;background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:8px;padding:10px 14px;color:#f5f1e8;font-size:13px;outline:none;max-width:460px;">
                <option value="">— Select a test —</option>
                @foreach($tests as $t)
                <option value="{{ $t->id }}" {{ $testId == $t->id ? 'selected' : '' }}>
                    {{ $t->test_code }} — {{ $t->name }} ({{ \Carbon\Carbon::parse($t->test_date)->format('d M Y') }})
                </option>
                @endforeach
            </select>
            @if($test)
            <span style="font-size:12px;color:#6a665f;">{{ $totalStudents }} students</span>
            @endif
        </div>
    </form>

    @if($test)

    {{-- Stats banner --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:20px;">
        @foreach([
            ['Highest', $stats->highest ?? '—', '#7fb685'],
            ['Lowest',  $stats->lowest  ?? '—', '#c87064'],
            ['Average', $stats ? round($stats->average,1) : '—', '#d4a574'],
            ['Median',  round($median,1), '#7a95c8'],
            ['Pass Rate',$passRate.'%', $passRate >= 60 ? '#7fb685' : ($passRate >= 35 ? '#d4a574' : '#c87064')],
        ] as [$lbl,$val,$col])
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:14px 16px;text-align:center;">
            <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;margin:0 0 4px;">{{ $lbl }}</p>
            <p style="font-size:22px;font-weight:800;color:{{ $col }};margin:0;letter-spacing:-0.4px;">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    {{-- Search + batch filter --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:16px;">
        <input type="hidden" name="test_id" value="{{ $testId }}">
        <input name="search" value="{{ $search }}" placeholder="Search by name or roll…"
            style="flex:1;background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:8px;padding:9px 14px;color:#f5f1e8;font-size:13px;outline:none;"
            onfocus="this.style.borderColor='#a392c8'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'">
        <select name="batch_id" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:8px;padding:9px 12px;color:#f5f1e8;font-size:13px;outline:none;">
            <option value="">All Batches</option>
            @foreach($batches as $b)
            <option value="{{ $b->id }}" {{ $batchFilter == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
        <button type="submit" style="background:#a392c8;border:none;border-radius:8px;padding:9px 18px;color:#08080a;font-size:13px;font-weight:700;cursor:pointer;">Filter</button>
        @if($search || $batchFilter)
        <a href="{{ route('owner.rankings', ['test_id'=>$testId]) }}" style="background:transparent;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:9px 14px;color:#6a665f;font-size:13px;text-decoration:none;display:flex;align-items:center;">Clear</a>
        @endif
    </form>

    {{-- Rank table --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid rgba(245,241,232,0.08);">
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 16px;font-weight:700;">Rank</th>
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Roll</th>
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Student</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Batch</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Score</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">✓/✗/—</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">%ile</th>
                    <th style="text-align:right;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 16px;font-weight:700;">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($results as $r)
                @php
                    $rank       = $r->rank_in_batch;
                    $rankLabel  = $rank <= 3 ? ['🏆','🥈','🥉'][$rank-1] : '#'.$rank;
                    $rankColor  = $rank === 1 ? '#d4a574' : ($rank <= 3 ? '#a8a39c' : '#6a665f');
                    $pct        = $maxMarks > 0 ? (int)round($r->total_marks / $maxMarks * 100) : 0;
                    $pc         = $pct >= 60 ? '#7fb685' : ($pct >= 35 ? '#d4a574' : '#c87064');
                @endphp
                <tr style="border-bottom:1px solid rgba(245,241,232,0.04);"
                    onmouseover="this.style.background='rgba(26,26,36,0.6)'" onmouseout="this.style.background=''">
                    <td style="text-align:center;padding:12px 16px;font-size:14px;font-weight:800;color:{{ $rankColor }};">{{ $rankLabel }}</td>
                    <td style="padding:12px 10px;font-size:11px;color:#6a665f;font-family:monospace;">{{ $r->roll_number }}</td>
                    <td style="padding:12px 10px;font-size:13px;font-weight:600;color:#f5f1e8;">{{ $r->student_name }}</td>
                    <td style="text-align:center;padding:12px 10px;">
                        <span style="font-size:10px;color:#a392c8;background:rgba(163,146,200,0.1);border-radius:4px;padding:2px 7px;">{{ $r->batch_name }}</span>
                    </td>
                    <td style="text-align:center;padding:12px 10px;font-size:16px;font-weight:800;color:{{ $pc }};">{{ $r->total_marks }}</td>
                    <td style="text-align:center;padding:12px 10px;font-size:11px;color:#6a665f;">
                        <span style="color:#7fb685;">{{ $r->total_correct }}</span>/
                        <span style="color:#c87064;">{{ $r->total_incorrect }}</span>/
                        <span style="color:#4a4740;">{{ $r->total_unattempted }}</span>
                    </td>
                    <td style="text-align:center;padding:12px 10px;font-size:12px;color:#a392c8;">{{ round($r->percentile,1) }}%</td>
                    <td style="text-align:right;padding:12px 16px;">
                        <a href="{{ route('owner.student.deep-dive', $r->student_id) }}"
                           style="font-size:11px;font-weight:600;color:#a392c8;text-decoration:none;background:rgba(163,146,200,0.1);border-radius:5px;padding:5px 10px;border:1px solid rgba(163,146,200,0.2);">
                            View →
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:48px;color:#6a665f;font-size:14px;">No results found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($results->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center;">{{ $results->links() }}</div>
    @endif

    @else
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:60px;text-align:center;">
        <p style="font-size:14px;color:#6a665f;">Select a test above to view rankings.</p>
    </div>
    @endif

</div>
@endsection
