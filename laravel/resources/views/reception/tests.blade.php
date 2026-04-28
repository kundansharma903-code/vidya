@extends('layouts.reception')
@section('title', 'All Tests')
@section('breadcrumb', 'All Tests')

@section('content')
<div style="max-width:1040px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">All Tests</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Click any test to see rank-wise student results</p>
    </div>

    <form method="GET" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        <input name="search" value="{{ $search }}" placeholder="Test name or code…"
            style="flex:1;min-width:200px;background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:8px;padding:10px 14px;color:#f5f1e8;font-size:14px;outline:none;"
            onfocus="this.style.borderColor='#c87064'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'">
        <input type="date" name="date_from" value="{{ $dateFrom }}" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:8px;padding:10px 12px;color:#f5f1e8;font-size:13px;outline:none;color-scheme:dark;">
        <input type="date" name="date_to" value="{{ $dateTo }}" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:8px;padding:10px 12px;color:#f5f1e8;font-size:13px;outline:none;color-scheme:dark;">
        <button type="submit" style="background:#c87064;border:none;border-radius:8px;padding:10px 20px;color:#fff;font-size:13px;font-weight:700;cursor:pointer;">Filter</button>
        @if($search || $dateFrom || $dateTo)
        <a href="{{ route('reception.tests') }}" style="background:transparent;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:10px 16px;color:#6a665f;font-size:13px;text-decoration:none;display:flex;align-items:center;">Clear</a>
        @endif
    </form>

    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid rgba(245,241,232,0.08);">
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 22px;font-weight:700;">Code</th>
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Test Name</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Date</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Batches</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Students</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Avg Score</th>
                    <th style="text-align:right;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 22px;font-weight:700;">Open</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tests as $test)
                @php
                    $stats = $statsMap->get($test->id);
                    $bc    = $batchCountMap->get($test->id);
                    $maxM  = $test->total_questions * 4;
                    $avg   = $stats ? round($stats->avg_marks, 1) : null;
                    $pct   = ($avg !== null && $maxM > 0) ? (int)round($avg / $maxM * 100) : null;
                    $pc    = $pct !== null ? ($pct >= 60 ? '#7fb685' : ($pct >= 35 ? '#d4a574' : '#c87064')) : '#6a665f';
                @endphp
                <tr style="border-bottom:1px solid rgba(245,241,232,0.04);cursor:pointer;transition:background 0.12s;"
                    onclick="window.location='{{ route('reception.test-results', $test->id) }}'"
                    onmouseover="this.style.background='rgba(245,241,232,0.03)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:13px 22px;font-size:11px;color:#c87064;font-weight:700;font-family:monospace;">{{ $test->test_code }}</td>
                    <td style="padding:13px 10px;">
                        <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">{{ $test->name }}</p>
                        <p style="font-size:10px;color:#6a665f;margin:0;">{{ $test->total_questions }} questions · {{ $maxM }} marks</p>
                    </td>
                    <td style="text-align:center;padding:13px 10px;font-size:12px;color:#a8a39c;">
                        {{ \Carbon\Carbon::parse($test->test_date)->format('d M Y') }}
                    </td>
                    <td style="text-align:center;padding:13px 10px;font-size:13px;color:#d4cfc8;">{{ $bc?->batch_count ?? '—' }}</td>
                    <td style="text-align:center;padding:13px 10px;font-size:13px;color:#d4cfc8;">{{ $stats?->students ?? '—' }}</td>
                    <td style="text-align:center;padding:13px 10px;font-size:14px;font-weight:700;color:{{ $pc }};">
                        {{ $avg !== null ? $avg : '—' }}
                    </td>
                    <td style="text-align:right;padding:13px 22px;">
                        <span style="font-size:11px;font-weight:600;color:#c87064;">Results →</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:48px;color:#6a665f;font-size:14px;">No analyzed tests found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tests->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center;">{{ $tests->links() }}</div>
    @endif
</div>
@endsection
