@extends('layouts.sub-admin')
@section('title', 'Result — ' . $student->name)
@section('breadcrumb', 'Student Result')

@section('content')
@php
$ss = $subjectBreakdown;
$totalMax = $test->total_questions * 4;
$marksColor = $result->total_marks >= ($totalMax * 0.6) ? '#7fb685' : ($result->total_marks >= ($totalMax * 0.3) ? '#d4a574' : '#c87064');
@endphp
<div style="max-width:960px;">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;font-size:13px;color:#6a665f;">
        <a href="{{ route('sub-admin.tests.index') }}" style="color:#6a665f;text-decoration:none;">All Tests</a>
        <span>›</span>
        <a href="{{ route('sub-admin.tests.results', $test->id) }}" style="color:#6a665f;text-decoration:none;">{{ $test->test_code }}</a>
        <span>›</span>
        <span style="color:#a8a39c;">{{ $student->name }}</span>
    </div>

    {{-- Student hero card --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:12px;padding:24px;margin-bottom:20px;display:flex;align-items:center;gap:24px;">

        {{-- Avatar --}}
        <div style="width:60px;height:60px;border-radius:50%;background:rgba(122,149,200,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:22px;font-weight:700;color:#7a95c8;">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>

        {{-- Info --}}
        <div style="flex:1;min-width:0;">
            <h2 style="font-size:22px;font-weight:700;color:#f5f1e8;letter-spacing:-0.44px;margin:0 0 4px;">{{ $student->name }}</h2>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <span style="background:#0f0f14;border-radius:4px;padding:3px 8px;font-size:11px;font-weight:500;color:#a8a39c;font-family:monospace;">{{ $student->roll_number }}</span>
                <span style="font-size:12px;color:#6a665f;">{{ $batch->name }}</span>
                <span style="font-size:12px;color:#6a665f;">{{ ucfirst($student->medium) }} medium</span>
            </div>
        </div>

        {{-- Score summary --}}
        <div style="display:flex;gap:24px;align-items:center;flex-shrink:0;">
            <div style="text-align:center;">
                <p style="font-size:36px;font-weight:800;color:{{ $marksColor }};letter-spacing:-0.72px;margin:0;">{{ $result->total_marks }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Marks</p>
            </div>
            <div style="width:1px;height:48px;background:rgba(245,241,232,0.08);"></div>
            <div style="text-align:center;">
                <p style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0;">#{{ $result->rank_in_batch }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Rank / {{ $totalInTest }}</p>
            </div>
            <div style="width:1px;height:48px;background:rgba(245,241,232,0.08);"></div>
            <div style="text-align:center;">
                <p style="font-size:28px;font-weight:700;color:#7a95c8;letter-spacing:-0.56px;margin:0;">{{ $result->percentile }}%</p>
                <p style="font-size:11px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Percentile</p>
            </div>
        </div>
    </div>

    {{-- Attempt breakdown --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;">
        <div style="background:#14141b;border:1px solid rgba(127,182,133,0.2);border-radius:8px;padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div style="width:40px;height:40px;border-radius:8px;background:rgba(127,182,133,0.12);display:flex;align-items:center;justify-content:center;font-size:18px;">✓</div>
            <div>
                <p style="font-size:26px;font-weight:700;color:#7fb685;margin:0 0 2px;letter-spacing:-0.52px;">{{ $result->total_correct }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Correct</p>
            </div>
        </div>
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.2);border-radius:8px;padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div style="width:40px;height:40px;border-radius:8px;background:rgba(200,112,100,0.12);display:flex;align-items:center;justify-content:center;font-size:18px;">✕</div>
            <div>
                <p style="font-size:26px;font-weight:700;color:#c87064;margin:0 0 2px;letter-spacing:-0.52px;">{{ $result->total_incorrect }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Wrong</p>
            </div>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 20px;display:flex;align-items:center;gap:14px;">
            <div style="width:40px;height:40px;border-radius:8px;background:rgba(245,241,232,0.04);display:flex;align-items:center;justify-content:center;font-size:18px;">–</div>
            <div>
                <p style="font-size:26px;font-weight:700;color:#a8a39c;margin:0 0 2px;letter-spacing:-0.52px;">{{ $result->total_unattempted }}</p>
                <p style="font-size:11px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Skipped</p>
            </div>
        </div>
    </div>

    {{-- Subject-wise breakdown --}}
    @if (!empty($ss))
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 24px;margin-bottom:20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Subject-wise Performance</p>
            <div style="display:flex;flex-direction:column;gap:14px;">
                @foreach ($ss as $subj)
                    @php
                        $maxSubjMarks = ($subj['correct'] + $subj['incorrect'] + $subj['unattempted']) * 4;
                        $pct = $maxSubjMarks > 0 ? max(0, round($subj['marks'] / $maxSubjMarks * 100)) : 0;
                        $barColor = $pct >= 60 ? '#7fb685' : ($pct >= 35 ? '#d4a574' : '#c87064');
                    @endphp
                    <div>
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span style="font-size:14px;font-weight:600;color:#f5f1e8;">{{ $subj['name'] }}</span>
                                <span style="font-size:11px;color:#6a665f;">C:{{ $subj['correct'] }} W:{{ $subj['incorrect'] }} U:{{ $subj['unattempted'] }}</span>
                            </div>
                            <div style="display:flex;align-items:center;gap:12px;">
                                <span style="font-size:15px;font-weight:700;color:{{ $barColor }};">{{ $subj['marks'] }}</span>
                                <span style="font-size:12px;color:#6a665f;">{{ $pct }}%</span>
                            </div>
                        </div>
                        <div style="height:6px;background:rgba(245,241,232,0.06);border-radius:3px;overflow:hidden;">
                            <div style="height:100%;background:{{ $barColor }};width:{{ $pct }}%;border-radius:3px;transition:width 0.4s;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Topic Mastery --}}
    @if (!empty($masteryBySubject))
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 24px;margin-bottom:20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Chapter-wise Mastery</p>

            @foreach ($masteryBySubject as $subjectName => $topics)
                <p style="font-size:12px;font-weight:600;color:#7a95c8;margin:0 0 10px;text-transform:uppercase;letter-spacing:0.88px;">{{ $subjectName }}</p>
                <div style="display:flex;flex-direction:column;gap:6px;margin-bottom:18px;">
                    @foreach ($topics as $t)
                        @php
                            $mp = (float)$t->mastery_percentage;
                            $mc = $mp >= 70 ? '#7fb685' : ($mp >= 40 ? '#d4a574' : '#c87064');
                        @endphp
                        <div style="display:grid;grid-template-columns:1fr 120px 80px 60px;gap:10px;align-items:center;padding:8px 12px;background:#0f0f14;border-radius:6px;">
                            <span style="font-size:12px;color:#f5f1e8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $t->topic_name }}</span>
                            <div style="height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                                <div style="height:100%;background:{{ $mc }};width:{{ $mp }}%;"></div>
                            </div>
                            <span style="font-size:11px;color:#6a665f;text-align:center;">{{ $t->total_questions_correct }}/{{ $t->total_questions_attempted }}</span>
                            <span style="font-size:12px;font-weight:600;color:{{ $mc }};text-align:right;">{{ $mp }}%</span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endif

    {{-- Answer review table --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 24px;">
        <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;">Answer Review ({{ $responses->count() }} questions)</p>

        <div style="display:grid;grid-template-columns:48px 80px 80px 80px 80px 80px;gap:8px;padding:8px 0;border-bottom:1px solid rgba(245,241,232,0.06);margin-bottom:6px;">
            @foreach(['Q#','TOPIC','CORRECT','YOURS','MARKS','RESULT'] as $col)
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">{{ $col }}</span>
            @endforeach
        </div>

        <div style="max-height:400px;overflow-y:auto;">
            @foreach ($responses as $resp)
                @php
                    $rc = $resp->is_correct === null ? '#a8a39c' : ($resp->is_correct ? '#7fb685' : '#c87064');
                    $rl = $resp->is_correct === null ? '–' : ($resp->is_correct ? '✓' : '✕');
                    $submitted = $resp->submitted_answer ?? '–';
                @endphp
                <div style="display:grid;grid-template-columns:48px 80px 80px 80px 80px 80px;gap:8px;padding:7px 0;border-bottom:1px solid rgba(245,241,232,0.03);align-items:center;">
                    <span style="font-size:12px;color:#6a665f;font-weight:500;">{{ $resp->question_number }}</span>
                    <span style="font-size:10px;color:#6a665f;font-family:monospace;">{{ $resp->topic_code }}</span>
                    <span style="font-size:12px;font-weight:600;color:#7fb685;">{{ $resp->correct_answer }}</span>
                    <span style="font-size:12px;font-weight:600;color:{{ $submitted === '–' ? '#6a665f' : $rc }};">{{ $submitted }}</span>
                    <span style="font-size:12px;color:{{ $resp->marks_awarded >= 0 ? '#7fb685' : '#c87064' }};">{{ $resp->marks_awarded >= 0 ? '+' : '' }}{{ $resp->marks_awarded }}</span>
                    <span style="font-size:13px;font-weight:700;color:{{ $rc }};">{{ $rl }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Footer nav --}}
    <div style="display:flex;justify-content:space-between;margin-top:20px;">
        <a href="{{ route('sub-admin.tests.results', $test->id) }}"
           style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:10px 16px;font-size:13px;font-weight:500;color:#a8a39c;text-decoration:none;">
            ← Back to Rankings
        </a>
    </div>

</div>
@endsection
