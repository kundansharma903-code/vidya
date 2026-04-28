@extends('layouts.reception')
@section('title', 'Help')
@section('breadcrumb', 'Help')

@section('content')
<div style="max-width:760px;">
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Reception Help Guide</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">How to help walk-in students</p>
    </div>

    @foreach([
        ['Student Result Lookup', 'Go to Search Students or use the Quick Lookup on Dashboard. Type the student\'s roll number or name. Click "View Result" on any row to see their full scorecard with Q-by-Q breakdown.'],
        ['"What is my rank?"', 'Open the test from All Tests → find the student in the rank-wise list. Rank is shown with 🏆 for top 3.'],
        ['"Which questions did I get wrong?"', 'Open Student Result Detail. The Q-by-Q grid shows correct (green), wrong (red), and unattempted (grey) answers with the correct answer shown below wrong ones.'],
        ['Print Report', 'On the Student Result Detail page, click the 🖨 Print Report button. The print view hides the sidebar and topbar automatically.'],
        ['Walk-in History', 'Go to Recent Walk-ins to see all students you looked up today, this week, or this month.'],
        ['What You Cannot Do', 'You cannot edit any data, view teacher performance or salaries, access topic mastery analysis, or see other reception staff\'s history.'],
    ] as [$title, $desc])
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 20px;margin-bottom:8px;">
        <p style="font-size:13px;font-weight:700;color:#c87064;margin:0 0 5px;">{{ $title }}</p>
        <p style="font-size:12px;color:#a8a39c;margin:0;line-height:1.7;">{{ $desc }}</p>
    </div>
    @endforeach
</div>
@endsection
