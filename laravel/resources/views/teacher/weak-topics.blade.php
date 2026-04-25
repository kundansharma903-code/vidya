@extends('layouts.teacher')
@section('title', 'Weak Topics')
@section('breadcrumb', 'Weak Topics')

@section('content')
<div style="max-width:960px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Weak Topics</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">{{ $subject?->name }} · Topics sorted by class average mastery (lowest first)</p>
    </div>

    @if ($topics->isEmpty())
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:64px;text-align:center;">
            <p style="font-size:14px;color:#6a665f;margin:0;">No topic data yet. Upload OMR results first.</p>
        </div>
    @else
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">

            <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:100px 1fr 140px 80px 80px 80px;gap:10px;padding:11px 20px;align-items:center;">
                @foreach(['CODE','TOPIC NAME','MASTERY','CORRECT','ATTEMPTED','STUDENTS'] as $col)
                    <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">{{ $col }}</span>
                @endforeach
            </div>

            @foreach ($topics as $t)
                @php
                    $mp = (int) round($t->avg_mastery);
                    $mc = $mp >= 70 ? '#7fb685' : ($mp >= 40 ? '#d4a574' : '#c87064');
                    $bg = $mp >= 70 ? 'rgba(127,182,133,0.08)' : ($mp >= 40 ? 'rgba(212,165,116,0.06)' : 'rgba(200,112,100,0.06)');
                @endphp
                <div style="display:grid;grid-template-columns:100px 1fr 140px 80px 80px 80px;gap:10px;padding:12px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                     onmouseover="this.style.background='rgba(26,26,36,0.5)'" onmouseout="this.style.background=''">

                    <a href="{{ route('teacher.topics.detail', $t->topic_code) }}"
                       style="font-size:11px;font-weight:600;color:#7a95c8;font-family:monospace;text-decoration:none;">{{ $t->topic_code }}</a>

                    <div>
                        <a href="{{ route('teacher.topics.detail', $t->topic_code) }}" style="font-size:13px;color:#f5f1e8;text-decoration:none;">{{ $t->topic_name }}</a>
                    </div>

                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="flex:1;height:5px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                            <div style="height:100%;background:{{ $mc }};width:{{ $mp }}%;"></div>
                        </div>
                        <span style="font-size:12px;font-weight:700;color:{{ $mc }};min-width:36px;text-align:right;">{{ $mp }}%</span>
                    </div>

                    <span style="font-size:13px;color:#7fb685;">{{ $t->total_correct }}</span>
                    <span style="font-size:13px;color:#a8a39c;">{{ $t->total_attempted }}</span>
                    <span style="font-size:13px;color:#6a665f;">{{ $t->student_count }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
