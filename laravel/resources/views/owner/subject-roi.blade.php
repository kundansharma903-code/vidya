@extends('layouts.owner')
@section('title', 'Subject ROI')
@section('breadcrumb', 'Subject ROI')

@section('content')
<div style="max-width:1060px;">
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Subject ROI</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Revenue-per-subject vs teacher cost analysis</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        @forelse($subjectData as $sd)
        @php
            $roiColor = $sd['roi'] >= 5 ? '#7fb685' : ($sd['roi'] >= 2.5 ? '#d4a574' : '#c87064');
            $mc = $sd['classAvg'] >= 60 ? '#7fb685' : ($sd['classAvg'] >= 40 ? '#d4a574' : '#c87064');
        @endphp
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
                <div>
                    <h2 style="font-size:17px;font-weight:700;color:#f5f1e8;margin:0;">{{ $sd['subject']->name }}</h2>
                    <p style="font-size:11px;color:#6a665f;margin:0;">{{ $sd['teachers']->count() }} teacher(s) · {{ $sd['students'] }} students</p>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:24px;font-weight:800;color:{{ $roiColor }};margin:0;letter-spacing:-0.5px;">{{ $sd['roi'] }}x</p>
                    <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0;">ROI</p>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px;">
                <div style="background:#0f0f14;border-radius:6px;padding:9px 10px;">
                    <p style="font-size:8px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">Annual Rev</p>
                    <p style="font-size:13px;font-weight:700;color:#7fb685;margin:0;">₹{{ number_format($sd['annualRevenue']) }}</p>
                </div>
                <div style="background:#0f0f14;border-radius:6px;padding:9px 10px;">
                    <p style="font-size:8px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">Annual Cost</p>
                    <p style="font-size:13px;font-weight:700;color:#d4a574;margin:0;">₹{{ number_format($sd['annualSalary']) }}</p>
                </div>
                <div style="background:#0f0f14;border-radius:6px;padding:9px 10px;">
                    <p style="font-size:8px;color:#6a665f;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 2px;">Net</p>
                    <p style="font-size:13px;font-weight:700;color:{{ $sd['netContribution'] >= 0 ? '#7fb685' : '#c87064' }};margin:0;">₹{{ number_format($sd['netContribution']) }}</p>
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                <span style="font-size:11px;color:#6a665f;">Academic Avg</span>
                <span style="font-size:12px;font-weight:600;color:{{ $mc }};">{{ $sd['classAvg'] }}%</span>
            </div>
            <div style="height:5px;background:rgba(245,241,232,0.06);border-radius:3px;overflow:hidden;margin-bottom:14px;">
                <div style="height:100%;background:{{ $mc }};width:{{ $sd['classAvg'] }}%;"></div>
            </div>

            @if($sd['teachers']->count() > 0)
            <div style="padding-top:12px;border-top:1px solid rgba(245,241,232,0.06);display:flex;align-items:center;justify-content:space-between;">
                <div style="display:flex;gap:6px;">
                    @foreach($sd['teachers'] as $t)
                    <span style="font-size:10px;color:#a8a39c;background:rgba(245,241,232,0.05);border-radius:4px;padding:3px 8px;">{{ explode(' ', $t->name)[0] }}</span>
                    @endforeach
                </div>
                <a href="{{ route('owner.subject-roi.detail', $sd['subject']->id) }}" style="font-size:11px;font-weight:600;color:#a392c8;text-decoration:none;background:rgba(163,146,200,0.1);border-radius:4px;padding:5px 10px;">
                    Deep Analysis →
                </a>
            </div>
            @endif
        </div>
        @empty
        <div style="grid-column:1/-1;text-align:center;padding:40px;background:#14141b;border-radius:10px;">
            <p style="color:#6a665f;font-size:14px;">No subjects found.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
