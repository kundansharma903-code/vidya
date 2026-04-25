@extends('layouts.teacher')
@section('title', $student->name)
@section('breadcrumb', 'Student Detail')

@section('content')
<div style="max-width:960px;">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;font-size:13px;color:#6a665f;">
        <a href="{{ route('teacher.students') }}" style="color:#6a665f;text-decoration:none;">My Students</a>
        <span>›</span>
        <span style="color:#a8a39c;">{{ $student->name }}</span>
    </div>

    {{-- Hero card --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:12px;padding:24px;margin-bottom:20px;display:flex;align-items:center;gap:24px;">
        <div style="width:56px;height:56px;border-radius:50%;background:rgba(127,182,133,0.15);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#7fb685;flex-shrink:0;">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <h2 style="font-size:20px;font-weight:700;color:#f5f1e8;margin:0 0 4px;">{{ $student->name }}</h2>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span style="background:#0f0f14;border-radius:4px;padding:2px 8px;font-size:11px;color:#a8a39c;font-family:monospace;">{{ $student->roll_number }}</span>
                <span style="font-size:12px;color:#6a665f;">{{ $student->batch_name }}</span>
                <span style="font-size:12px;color:#6a665f;">{{ ucfirst($student->medium) }} medium</span>
            </div>
        </div>
        <div style="text-align:center;flex-shrink:0;">
            @php
                $mc = $avgMastery >= 70 ? '#7fb685' : ($avgMastery >= 40 ? '#d4a574' : '#c87064');
            @endphp
            <p style="font-size:34px;font-weight:800;color:{{ $mc }};margin:0;letter-spacing:-0.68px;">{{ $avgMastery }}%</p>
            <p style="font-size:10px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Avg Mastery · {{ $subject?->name }}</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

        {{-- Topic Mastery --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Topic-wise Mastery · {{ $subject?->name }}</p>

            @forelse ($topicMastery as $t)
                @php
                    $mp = (float) $t->mastery_percentage;
                    $tc = $mp >= 70 ? '#7fb685' : ($mp >= 40 ? '#d4a574' : '#c87064');
                @endphp
                <div style="margin-bottom:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <div style="display:flex;align-items:center;gap:8px;min-width:0;">
                            <span style="font-size:10px;color:#6a665f;font-family:monospace;flex-shrink:0;">{{ $t->topic_code }}</span>
                            <span style="font-size:12px;color:#f5f1e8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $t->topic_name }}</span>
                        </div>
                        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;margin-left:8px;">
                            <span style="font-size:10px;color:#6a665f;">{{ $t->total_questions_correct }}/{{ $t->total_questions_attempted }}</span>
                            <span style="font-size:12px;font-weight:600;color:{{ $tc }};min-width:36px;text-align:right;">{{ (int)$mp }}%</span>
                        </div>
                    </div>
                    <div style="height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                        <div style="height:100%;background:{{ $tc }};width:{{ $mp }}%;"></div>
                    </div>
                </div>
            @empty
                <p style="font-size:13px;color:#6a665f;margin:20px 0;text-align:center;">No mastery data yet.</p>
            @endforelse
        </div>

        {{-- Test History --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Test History</p>

            @forelse ($testHistory as $th)
                @php
                    $maxM = $th->total_questions * 4;
                    $pct  = $maxM > 0 ? round($th->total_marks / $maxM * 100) : 0;
                    $tc   = $pct >= 60 ? '#7fb685' : ($pct >= 35 ? '#d4a574' : '#c87064');
                @endphp
                <div style="padding:10px 12px;background:#0f0f14;border-radius:6px;margin-bottom:8px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span style="font-size:10px;font-weight:600;color:#a8a39c;font-family:monospace;">{{ $th->test_code }}</span>
                            <span style="font-size:12px;color:#f5f1e8;">{{ $th->name }}</span>
                        </div>
                        <span style="font-size:14px;font-weight:700;color:{{ $tc }};">{{ $th->total_marks }}</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span style="font-size:10px;color:#6a665f;">{{ \Carbon\Carbon::parse($th->test_date)->format('d M Y') }}</span>
                        <span style="font-size:10px;color:#7fb685;">C:{{ $th->total_correct }}</span>
                        <span style="font-size:10px;color:#c87064;">W:{{ $th->total_incorrect }}</span>
                        <span style="font-size:10px;color:#6a665f;">Rank #{{ $th->rank_in_batch }}</span>
                        <span style="font-size:10px;color:#a8a39c;">{{ $th->percentile }}%ile</span>
                    </div>
                </div>
            @empty
                <p style="font-size:13px;color:#6a665f;margin:20px 0;text-align:center;">No test history yet.</p>
            @endforelse
        </div>

    </div>
</div>
@endsection
