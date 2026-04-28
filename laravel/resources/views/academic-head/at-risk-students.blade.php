@extends('layouts.academic-head')
@section('title', 'At-Risk Students')
@section('breadcrumb', 'At-Risk Students')

@section('content')
<div style="max-width:1060px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">At-Risk Students</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Students with average mastery below 40% · Requires immediate attention</p>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('academic-head.at-risk-students') }}" style="display:flex;gap:10px;margin-bottom:18px;align-items:center;flex-wrap:wrap;">
        <div style="position:relative;flex:1;min-width:200px;">
            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:13px;color:#6a665f;">🔍</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or roll…"
                   style="width:100%;box-sizing:border-box;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:6px;padding:10px 14px 10px 34px;font-size:13px;color:#f5f1e8;outline:none;"
                   onfocus="this.style.borderColor='rgba(200,112,100,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.08)'">
        </div>
        <select name="batch_id" onchange="this.form.submit()"
                style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;cursor:pointer;min-width:150px;">
            <option value="">All Batches</option>
            @foreach ($batches as $b)
                <option value="{{ $b->id }}" {{ $batchFilter == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
        <select name="subject_id" onchange="this.form.submit()"
                style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;cursor:pointer;min-width:150px;">
            <option value="">All Subjects</option>
            @foreach ($subjects as $s)
                <option value="{{ $s->id }}" {{ $subjectFilter == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>
        @if ($search || $batchFilter || $subjectFilter)
            <a href="{{ route('academic-head.at-risk-students') }}" style="font-size:12px;color:#6a665f;text-decoration:none;padding:10px 4px;">Clear ✕</a>
        @endif
    </form>

    @if ($atRisk->isEmpty())
        <div style="background:rgba(127,182,133,0.06);border:1px solid rgba(127,182,133,0.2);border-radius:10px;padding:64px;text-align:center;">
            <p style="font-size:28px;margin:0 0 10px;">🎉</p>
            <p style="font-size:15px;font-weight:600;color:#7fb685;margin:0 0 4px;">No at-risk students found!</p>
            <p style="font-size:12px;color:#6a665f;margin:0;">All students are above the 40% mastery threshold{{ $batchFilter || $subjectFilter ? ' for selected filters' : '' }}.</p>
        </div>
    @else
        <div style="background:rgba(200,112,100,0.05);border:1px solid rgba(200,112,100,0.15);border-radius:6px;padding:10px 16px;margin-bottom:14px;display:flex;align-items:center;gap:8px;">
            <span style="font-size:14px;">⚠️</span>
            <p style="font-size:12px;color:#c87064;margin:0;"><strong>{{ $atRisk->count() }} student{{ $atRisk->count() !== 1 ? 's' : '' }}</strong> at risk — avg mastery below 40%</p>
        </div>

        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
            <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:110px 1fr 140px 140px 80px;gap:10px;padding:11px 20px;">
                @foreach(['ROLL','NAME','BATCH','MASTERY','TOPICS'] as $col)
                    <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">{{ $col }}</span>
                @endforeach
            </div>

            @foreach ($atRisk as $s)
                @php
                    $mc = $s->avg_m >= 30 ? '#d4a574' : '#c87064';
                @endphp
                <div style="display:grid;grid-template-columns:110px 1fr 140px 140px 80px;gap:10px;padding:11px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                     onmouseover="this.style.background='rgba(26,26,36,0.5)'" onmouseout="this.style.background=''">
                    <span style="font-size:11px;color:#6a665f;font-family:monospace;">{{ $s->roll_number }}</span>
                    <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0;">{{ $s->name }}</p>
                    <span style="font-size:11px;color:#a8a39c;">{{ $s->batch_name }}</span>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="flex:1;height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                            <div style="height:100%;background:{{ $mc }};width:{{ $s->avg_m }}%;"></div>
                        </div>
                        <span style="font-size:12px;font-weight:700;color:{{ $mc }};min-width:32px;text-align:right;">{{ $s->avg_m }}%</span>
                    </div>
                    <span style="font-size:12px;color:#6a665f;">{{ $s->topics }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
