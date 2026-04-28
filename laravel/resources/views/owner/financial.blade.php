@extends('layouts.owner')
@section('title', 'Financial Summary')
@section('breadcrumb', 'Financial Summary')

@section('content')
@php
    $marginColor = $margin >= 35 ? '#7fb685' : ($margin >= 20 ? '#d4a574' : '#c87064');
    $profitColor = $annualProfit >= 0 ? '#7fb685' : '#c87064';
@endphp
<div style="max-width:1060px;">
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Financial Summary</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">P&L breakdown · FY {{ now()->year }}</p>
    </div>

    {{-- Top KPIs --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;">
        @foreach([
            ['Annual Revenue', '₹'.number_format($annualRevenue), '#7fb685'],
            ['Annual Cost',    '₹'.number_format($annualCost),    '#d4a574'],
            ['Net Profit',     '₹'.number_format($annualProfit),  $profitColor],
            ['Margin',         $margin.'%',                       $marginColor],
        ] as [$lbl,$val,$col])
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
            <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1.1px;margin:0 0 6px;">{{ $lbl }}</p>
            <p style="font-size:26px;font-weight:800;color:{{ $col }};letter-spacing:-0.8px;margin:0;">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    {{-- Monthly --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px;">
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
            <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1.1px;margin:0 0 6px;">Monthly Revenue</p>
            <p style="font-size:26px;font-weight:800;color:#7fb685;letter-spacing:-0.8px;margin:0 0 4px;">₹{{ number_format($monthlyRevenue) }}</p>
            <p style="font-size:11px;color:#4a4740;">From {{ collect($batchBreakdown)->where('annualRevenue','>',0)->count() }} fee-paying batches</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
            <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1.1px;margin:0 0 6px;">Monthly Cost</p>
            <p style="font-size:26px;font-weight:800;color:#d4a574;letter-spacing:-0.8px;margin:0 0 4px;">₹{{ number_format($monthlyCost) }}</p>
            <p style="font-size:11px;color:#4a4740;">{{ $staffBreakdown->count() }} staff salaries</p>
        </div>
    </div>

    {{-- Revenue by batch --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">
            <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0 0 16px;">Revenue by Batch</h2>
            @foreach($batchBreakdown as $bb)
            @php $barPct = $annualRevenue > 0 ? round($bb['annualRevenue']/$annualRevenue*100) : 0; @endphp
            <div style="margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <div>
                        <span style="font-size:12px;color:#d4cfc8;font-weight:500;">{{ $bb['batch']->name }}</span>
                        <span style="font-size:10px;color:#6a665f;margin-left:6px;">{{ $bb['students'] }} students · ₹{{ number_format($bb['monthly_fee']) }}/mo</span>
                    </div>
                    <span style="font-size:12px;font-weight:600;color:#7fb685;">₹{{ number_format($bb['annualRevenue']) }}</span>
                </div>
                <div style="height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                    <div style="height:100%;background:#7fb685;width:{{ $barPct }}%;opacity:0.7;"></div>
                </div>
            </div>
            @endforeach
        </div>

        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">
            <h2 style="font-size:14px;font-weight:700;color:#f5f1e8;margin:0 0 16px;">Staff Cost Breakdown</h2>
            @foreach($staffBreakdown as $staff)
            @php $salShare = $monthlyCost > 0 ? round(($staff->monthly_salary ?? 0) / $monthlyCost * 100) : 0; @endphp
            <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                <div>
                    <p style="font-size:13px;color:#d4cfc8;font-weight:500;margin:0;">{{ $staff->name }}</p>
                    <p style="font-size:10px;color:#6a665f;margin:0;text-transform:capitalize;">{{ str_replace('_',' ',$staff->role) }} · {{ $salShare }}% of total</p>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:13px;font-weight:600;color:#d4a574;margin:0;">₹{{ number_format($staff->monthly_salary ?? 0) }}/mo</p>
                    <p style="font-size:10px;color:#6a665f;margin:0;">₹{{ number_format(($staff->monthly_salary ?? 0)*12) }}/yr</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
