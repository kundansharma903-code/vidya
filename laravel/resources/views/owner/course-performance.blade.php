@extends('layouts.owner')
@section('title', 'Course Performance')
@section('breadcrumb', 'Course Performance')

@section('content')
<div style="max-width:1060px;">
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Course Performance</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Revenue and academic performance by course</p>
    </div>

    {{-- Summary banner --}}
    <div style="background:rgba(163,146,200,0.08);border:1px solid rgba(163,146,200,0.2);border-radius:10px;padding:16px 22px;margin-bottom:22px;display:flex;gap:32px;">
        <div>
            <p style="font-size:9px;color:#a392c8;text-transform:uppercase;letter-spacing:1.1px;margin:0 0 3px;">Total Annual Revenue</p>
            <p style="font-size:22px;font-weight:800;color:#a392c8;margin:0;letter-spacing:-0.5px;">₹{{ number_format($totalAnnualRevenue) }}</p>
        </div>
        <div>
            <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1.1px;margin:0 0 3px;">Total Annual Cost</p>
            <p style="font-size:22px;font-weight:800;color:#d4a574;margin:0;letter-spacing:-0.5px;">₹{{ number_format($totalAnnualCost) }}</p>
        </div>
        <div>
            <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1.1px;margin:0 0 3px;">Net Profit</p>
            @php $profit = $totalAnnualRevenue - $totalAnnualCost; @endphp
            <p style="font-size:22px;font-weight:800;color:{{ $profit >= 0 ? '#7fb685' : '#c87064' }};margin:0;letter-spacing:-0.5px;">₹{{ number_format($profit) }}</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        @forelse($courseData as $cd)
        @php
            $mc = $cd['classAvg'] >= 60 ? '#7fb685' : ($cd['classAvg'] >= 40 ? '#d4a574' : '#c87064');
            $revShare = $totalAnnualRevenue > 0 ? round($cd['annualRevenue'] / $totalAnnualRevenue * 100) : 0;
        @endphp
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div>
                    <h2 style="font-size:17px;font-weight:700;color:#f5f1e8;margin:0;">{{ $cd['course']->name }}</h2>
                    <p style="font-size:11px;color:#6a665f;margin:0;">{{ $cd['course']->code }} · {{ $cd['totalStudents'] }} students · {{ $cd['batches']->count() }} batches</p>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:18px;font-weight:800;color:#a392c8;margin:0;">{{ $revShare }}%</p>
                    <p style="font-size:10px;color:#6a665f;margin:0;">of revenue</p>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px;">
                <div style="background:#0f0f14;border-radius:6px;padding:9px 10px;text-align:center;">
                    <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">Annual Rev</p>
                    <p style="font-size:13px;font-weight:700;color:#7fb685;margin:0;">₹{{ number_format($cd['annualRevenue']) }}</p>
                </div>
                <div style="background:#0f0f14;border-radius:6px;padding:9px 10px;text-align:center;">
                    <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">Class Avg</p>
                    <p style="font-size:13px;font-weight:700;color:{{ $mc }};margin:0;">{{ $cd['classAvg'] }}%</p>
                </div>
                <div style="background:#0f0f14;border-radius:6px;padding:9px 10px;text-align:center;">
                    <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">Coverage</p>
                    <p style="font-size:13px;font-weight:700;color:#7a95c8;margin:0;">{{ $cd['coveragePct'] }}%</p>
                </div>
            </div>

            {{-- Revenue bar --}}
            <div style="margin-bottom:8px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:10px;color:#6a665f;">Revenue Share</span>
                    <span style="font-size:11px;font-weight:600;color:#a392c8;">{{ $revShare }}%</span>
                </div>
                <div style="height:5px;background:rgba(245,241,232,0.06);border-radius:3px;overflow:hidden;">
                    <div style="height:100%;background:#a392c8;width:{{ $revShare }}%;opacity:0.8;"></div>
                </div>
            </div>

            {{-- Mastery bar --}}
            <div>
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span style="font-size:10px;color:#6a665f;">Academic Performance</span>
                    <span style="font-size:11px;font-weight:600;color:{{ $mc }};">{{ $cd['classAvg'] }}%</span>
                </div>
                <div style="height:5px;background:rgba(245,241,232,0.06);border-radius:3px;overflow:hidden;">
                    <div style="height:100%;background:{{ $mc }};width:{{ $cd['classAvg'] }}%;"></div>
                </div>
            </div>

            {{-- Batches --}}
            <div style="margin-top:14px;padding-top:12px;border-top:1px solid rgba(245,241,232,0.06);">
                <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;margin:0 0 6px;">Batches</p>
                <div style="display:flex;flex-wrap:wrap;gap:5px;">
                    @foreach($cd['batches'] as $b)
                    <span style="font-size:10px;color:#a8a39c;background:rgba(245,241,232,0.05);border-radius:4px;padding:3px 7px;">{{ $b->code }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        @empty
        <div style="grid-column:1/-1;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:40px;text-align:center;">
            <p style="color:#6a665f;font-size:14px;margin:0;">No active courses found.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
