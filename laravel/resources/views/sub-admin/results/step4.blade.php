@extends('layouts.sub-admin')
@section('title', 'Upload Results — Analyze')
@section('breadcrumb', 'Analyze & Process')

@section('content')
@php
$routeExists = function(string $name): bool {
    try { route($name); return true; } catch (\Exception $e) { return false; }
};
$v = $validation;
@endphp
<div style="max-width:960px;">

    {{-- Page heading --}}
    <div style="margin-bottom:24px;">
        <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px;">Analyze & Process Results</h1>
        <p style="font-size:14px;color:#a8a39c;margin:0;">Step 4 of 4 — Run scoring engine to compute marks, ranks, and topic mastery</p>
    </div>

    {{-- Stepper --}}
    @include('sub-admin.results._stepper', ['currentStep' => 4])

    {{-- Selected test card --}}
    <div style="background:#14141b;border:1px solid rgba(122,149,200,0.3);border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:20px;margin-bottom:20px;">
        <div style="width:10px;height:10px;border-radius:50%;background:#7a95c8;flex-shrink:0;"></div>
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:3px;">
                <span style="background:#0f0f14;border-radius:4px;padding:3px 8px;font-size:11px;font-weight:500;color:#a8a39c;">{{ $test->test_code }}</span>
                <span style="font-size:15px;font-weight:600;color:#f5f1e8;">{{ $test->name }}</span>
                @if ($test->course_label)
                    <span style="background:rgba(122,149,200,0.12);border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:500;color:#7a95c8;">{{ $test->course_label }}</span>
                @endif
            </div>
            <span style="font-size:12px;color:#a8a39c;">{{ \Carbon\Carbon::parse($test->test_date)->format('d M Y') }} · {{ $test->total_questions }} questions</span>
        </div>
    </div>

    {{-- Analysis summary --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;margin-bottom:20px;">
        <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 18px;">What will be processed</p>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
            <div style="background:#0f0f14;border-radius:8px;padding:16px 18px;">
                <p style="font-size:26px;font-weight:700;color:#7fb685;letter-spacing:-0.52px;margin:0 0 4px;">{{ $processCount }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Students Scored</p>
            </div>
            <div style="background:#0f0f14;border-radius:8px;padding:16px 18px;">
                <p style="font-size:26px;font-weight:700;color:#a8a39c;letter-spacing:-0.52px;margin:0 0 4px;">{{ $absentCount }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Marked Absent</p>
            </div>
            <div style="background:#0f0f14;border-radius:8px;padding:16px 18px;">
                <p style="font-size:26px;font-weight:700;color:#7a95c8;letter-spacing:-0.52px;margin:0 0 4px;">{{ $test->total_questions }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Questions</p>
            </div>
        </div>

        <div style="border-top:1px solid rgba(245,241,232,0.06);padding-top:16px;">
            <p style="font-size:12px;font-weight:500;color:#a8a39c;margin:0 0 10px;">What gets computed:</p>
            <div style="display:flex;flex-direction:column;gap:7px;">
                @foreach([
                    ['✓', '#7fb685', 'Marks per question (correct +4, incorrect −1, unattempted 0, invalid −1)'],
                    ['✓', '#7fb685', 'Total marks, correct, incorrect, unattempted per student'],
                    ['✓', '#7fb685', 'Subject-wise breakdown (Physics, Chemistry, Botany, Zoology)'],
                    ['✓', '#7fb685', 'Batch rank and percentile for every student'],
                    ['✓', '#7fb685', 'Topic mastery update per student per chapter'],
                    ['✓', '#7fb685', 'Test status set to Analyzed'],
                ] as [$icon, $color, $text])
                    <div style="display:flex;align-items:flex-start;gap:10px;">
                        <span style="color:{{ $color }};font-size:13px;font-weight:700;flex-shrink:0;margin-top:1px;">{{ $icon }}</span>
                        <span style="font-size:13px;color:#a8a39c;line-height:1.5;">{{ $text }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Upload file info --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:8px;padding:12px 18px;display:flex;align-items:center;gap:12px;margin-bottom:20px;">
        <span style="font-size:18px;">📄</span>
        <div style="flex:1;">
            <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0 0 1px;">{{ $v['file_name'] }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">{{ number_format($v['file_size'] / 1024, 1) }} KB · {{ $v['total'] }} response rows</p>
        </div>
        <span style="font-size:11px;color:#7fb685;font-weight:500;">Ready</span>
    </div>

    {{-- Warning --}}
    <div style="background:rgba(200,112,100,0.08);border:1px solid rgba(200,112,100,0.2);border-radius:6px;padding:12px 16px;display:flex;align-items:flex-start;gap:10px;margin-bottom:24px;">
        <span style="color:#c87064;font-size:14px;font-weight:700;flex-shrink:0;margin-top:1px;">!</span>
        <p style="font-size:12px;color:#a8a39c;margin:0;line-height:1.6;">
            This operation cannot be undone easily. If you re-run analysis on the same test, previous results will be overwritten. Make sure the uploaded file is correct before proceeding.
        </p>
    </div>

    {{-- Footer --}}
    <div style="display:flex;align-items:center;justify-content:space-between;padding-top:4px;">
        @if (!empty($validation['unmatched']))
            <a href="{{ route('sub-admin.results.upload.map', $test->id) }}"
               style="font-size:13px;color:#6a665f;text-decoration:none;">← Back to Mapping</a>
        @else
            <a href="{{ route('sub-admin.results.upload.file', $test->id) }}"
               style="font-size:13px;color:#6a665f;text-decoration:none;">← Back to Upload</a>
        @endif

        <div style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('sub-admin.dashboard') }}"
               style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:10px 16px;font-size:13px;font-weight:500;color:#a8a39c;text-decoration:none;">
                Cancel
            </a>
            <form method="POST" action="{{ route('sub-admin.results.upload.run', $test->id) }}" id="analyzeForm">
                @csrf
                <button type="submit" id="analyzeBtn"
                        style="background:#7fb685;border:none;border-radius:6px;padding:10px 24px;font-size:13px;font-weight:700;color:#14141b;cursor:pointer;display:flex;align-items:center;gap:8px;box-shadow:0 2px 8px rgba(127,182,133,0.25);">
                    <span id="analyzeBtnText">🚀 Run Analysis</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('analyzeForm').addEventListener('submit', function() {
    const btn  = document.getElementById('analyzeBtn');
    const text = document.getElementById('analyzeBtnText');
    btn.disabled      = true;
    btn.style.opacity = '0.7';
    text.textContent  = '⏳ Processing… please wait';
});
</script>
@endsection
