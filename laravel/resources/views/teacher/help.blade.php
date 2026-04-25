@extends('layouts.teacher')
@section('title', 'Help')
@section('breadcrumb', 'Help')

@section('content')
<div style="max-width:720px;">

    <div style="margin-bottom:26px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Help & Guide</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">How to use the Teacher Panel</p>
    </div>

    @php
        $sections = [
            ['Dashboard', 'dashboard', 'Your home screen. Shows subject banner with average mastery, key stats (students, tests, at-risk), recent test scores, and your 5 weakest topics at a glance.'],
            ['My Students', 'students', 'View all students in your batches. See each student\'s average mastery percentage for your subject. Click "View →" to open a student\'s full topic-wise breakdown and test history.'],
            ['Class Heatmap', 'heatmap', 'A grid of Topic × Batch. Each cell shows the average mastery percentage for that topic in that batch. Green = strong (≥70%), Orange = average (40–69%), Red = weak (<40%). Click a topic name to see individual student performance.'],
            ['Class Insights', 'insights', 'Aggregated view: mastery distribution chart, top 5 performers, at-risk students (below 40% average), and test performance trend bars.'],
            ['Weak Topics', 'weak-topics', 'All topics sorted by class-average mastery, lowest first. See correct/attempted counts and how many students have data for each topic.'],
            ['My Tests', 'tests', 'All analyzed tests with key stats: student count, average score, highest, and lowest marks for your students.'],
        ];
    @endphp

    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach ($sections as [$title, $route, $desc])
            <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <p style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0;">{{ $title }}</p>
                    <a href="{{ route('teacher.'.$route) }}" style="font-size:11px;color:#7a95c8;text-decoration:none;">Open →</a>
                </div>
                <p style="font-size:13px;color:#a8a39c;margin:0;line-height:1.6;">{{ $desc }}</p>
            </div>
        @endforeach
    </div>

    <div style="background:rgba(122,149,200,0.06);border:1px solid rgba(122,149,200,0.15);border-radius:8px;padding:16px 20px;margin-top:16px;">
        <p style="font-size:13px;color:#7a95c8;margin:0;font-weight:600;">Need more help?</p>
        <p style="font-size:12px;color:#a8a39c;margin:4px 0 0;">Contact your institute admin. Mastery data is computed automatically after each OMR upload.</p>
    </div>
</div>
@endsection
