@extends('layouts.teacher')
@section('title', $node->name)
@section('breadcrumb', 'Topic Detail')

@section('content')
<div style="max-width:960px;">

    {{-- Breadcrumb --}}
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:20px;font-size:13px;color:#6a665f;">
        <a href="{{ route('teacher.weak-topics') }}" style="color:#6a665f;text-decoration:none;">Weak Topics</a>
        <span>›</span>
        <span style="color:#a8a39c;">{{ $node->code }}</span>
    </div>

    {{-- Header --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;">
        <div>
            <span style="font-size:12px;font-weight:600;color:#7a95c8;font-family:monospace;">{{ $node->code }}</span>
            <h1 style="font-size:22px;font-weight:700;color:#f5f1e8;letter-spacing:-0.44px;margin:4px 0 2px;">{{ $node->name }}</h1>
            <p style="font-size:13px;color:#a8a39c;margin:0;">{{ $subject?->name }} · {{ $students->count() }} students</p>
        </div>
        <div style="text-align:center;">
            @php $mc = $avgMastery >= 70 ? '#7fb685' : ($avgMastery >= 40 ? '#d4a574' : '#c87064'); @endphp
            <p style="font-size:36px;font-weight:800;color:{{ $mc }};margin:0;letter-spacing:-0.72px;">{{ $avgMastery }}%</p>
            <p style="font-size:10px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.88px;">Class Average</p>
        </div>
    </div>

    {{-- Student list --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
        <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:110px 1fr 140px 120px 80px;gap:10px;padding:11px 20px;align-items:center;">
            @foreach(['ROLL','NAME','BATCH','MASTERY','C/A'] as $col)
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">{{ $col }}</span>
            @endforeach
        </div>

        @forelse ($students as $s)
            @php
                $mp = (int) round($s->mastery_percentage);
                $mc = $mp >= 70 ? '#7fb685' : ($mp >= 40 ? '#d4a574' : '#c87064');
            @endphp
            <div style="display:grid;grid-template-columns:110px 1fr 140px 120px 80px;gap:10px;padding:11px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                 onmouseover="this.style.background='rgba(26,26,36,0.5)'" onmouseout="this.style.background=''">
                <span style="font-size:11px;color:#6a665f;font-family:monospace;">{{ $s->roll_number }}</span>
                <span style="font-size:13px;font-weight:500;color:#f5f1e8;">{{ $s->name }}</span>
                <span style="font-size:11px;color:#a8a39c;">{{ $s->batch_name }}</span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="flex:1;height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                        <div style="height:100%;background:{{ $mc }};width:{{ $mp }}%;"></div>
                    </div>
                    <span style="font-size:12px;font-weight:600;color:{{ $mc }};min-width:36px;text-align:right;">{{ $mp }}%</span>
                </div>
                <span style="font-size:12px;color:#a8a39c;">{{ $s->total_questions_correct }}/{{ $s->total_questions_attempted }}</span>
            </div>
        @empty
            <div style="padding:48px 20px;text-align:center;">
                <p style="font-size:14px;color:#6a665f;margin:0;">No student data for this topic.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
