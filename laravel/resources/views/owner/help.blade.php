@extends('layouts.owner')
@section('title', 'Help')
@section('breadcrumb', 'Help')

@section('content')
<div style="max-width:780px;">
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Owner Help Guide</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">How to use the Owner panel</p>
    </div>

    @foreach([
        ['Dashboard','Business Health Score is a weighted average of 4 pillars: financial margin (30%), academic performance (25%), student retention (25%), and curriculum coverage (20%). Health ≥75 is green, ≥50 is amber, below 50 is red.'],
        ['Financial Summary','Revenue = sum of (batch students × monthly_fee × 12). Cost = sum of all staff monthly salaries × 12. Profit Margin = (Revenue - Cost) / Revenue × 100. Target: ≥35%.'],
        ['Teacher Performance','Teachers are ranked by ROI (Annual Revenue generated / Annual Salary). Click any row to open the Teacher Deep-dive.'],
        ['Teacher Deep-Dive','Shows ROI, effectiveness score, strong/weak topics, batch-wise revenue, and test trend. Decision Panel suggests Promote / Raise / Training / Cross-pair based on live data.'],
        ['Subject ROI','Per-subject revenue vs teacher cost. Click "Deep Analysis" to compare side-by-side with topic-level breakdown and strategic recommendations.'],
        ['Strategic Alerts','Auto-generated alerts from live DB. Sorted critical → warning → success. Click "View →" to go directly to the relevant screen.'],
        ['Staff Decisions','Lists teachers with pending recommended actions. Approve/Dismiss buttons show a toast notification (currently no DB write — to be connected to HR workflow).'],
    ] as [$title,$desc])
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 20px;margin-bottom:8px;">
        <p style="font-size:13px;font-weight:700;color:#a392c8;margin:0 0 5px;">{{ $title }}</p>
        <p style="font-size:12px;color:#a8a39c;margin:0;line-height:1.7;">{{ $desc }}</p>
    </div>
    @endforeach
</div>
@endsection
