@extends('layouts.academic-head')
@section('title', 'Help')
@section('breadcrumb', 'Help')

@section('content')
<div style="max-width:720px;">
    <div style="margin-bottom:26px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Help & Guide</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Academic Head Panel — what each screen does</p>
    </div>

    @php
        $sections = [
            ['Dashboard',            'dashboard',            'Institute health score (0-100) combining curriculum coverage, test quality, teacher effectiveness, and student retention. KPIs, subject performance tiles, and academic alerts.'],
            ['Curriculum Coverage',  'curriculum-coverage',  'Subject-wise topic coverage with urgency banner, weeks-remaining counter, and critical gaps showing topics never tested.'],
            ['Test Quality',         'test-quality',         'Analyzes each test for topic diversity and coverage alignment. Grades tests A+/A/B/C. Includes improvement recommendations.'],
            ['Subject Performance',  'subject-performance',  'Deep-dive into each subject: average mastery, weak topic count, curriculum coverage, and correct/attempted stats.'],
            ['Teacher Effectiveness','teacher-effectiveness', 'Ranked list of all teachers by effectiveness score (class average − weak topic penalty). Top 3 podium + AI insight on underperformers.'],
            ['Teacher Assignments',  'teacher-assignments',  'Read-only view of which teacher covers which subject and which batches. Contact admin to modify.'],
            ['At-Risk Students',     'at-risk-students',     'Institute-wide list of students with average mastery below 40%. Filter by batch or subject to prioritize intervention.'],
        ];
    @endphp

    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach ($sections as [$title, $route, $desc])
            <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 20px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                    <p style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0;">{{ $title }}</p>
                    <a href="{{ route('academic-head.'.$route) }}" style="font-size:11px;color:#7a95c8;text-decoration:none;">Open →</a>
                </div>
                <p style="font-size:13px;color:#a8a39c;margin:0;line-height:1.6;">{{ $desc }}</p>
            </div>
        @endforeach
    </div>

    <div style="background:rgba(122,149,200,0.06);border:1px solid rgba(122,149,200,0.15);border-radius:8px;padding:16px 20px;margin-top:16px;">
        <p style="font-size:13px;color:#7a95c8;margin:0;font-weight:600;">Role scope</p>
        <p style="font-size:12px;color:#a8a39c;margin:4px 0 0;line-height:1.6;">Academic Head has <strong style="color:#f5f1e8;">read-only</strong> access to all institute data. No data can be added, edited, or deleted from this panel. Data updates automatically after each OMR upload by Sub-Admin.</p>
    </div>
</div>
@endsection
