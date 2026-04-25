@extends('layouts.teacher')
@section('title', 'My Tests')
@section('breadcrumb', 'My Tests')

@section('content')
<div style="max-width:1040px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">My Tests</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Analyzed tests with results for your students</p>
    </div>

    @if ($tests->isEmpty())
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:64px;text-align:center;">
            <p style="font-size:14px;color:#6a665f;margin:0;">No analyzed tests yet.</p>
        </div>
    @else
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">

            <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:120px 1fr 100px 80px 80px 80px 80px;gap:10px;padding:11px 20px;align-items:center;">
                @foreach(['CODE','NAME','DATE','STUDENTS','AVG','HIGH','LOW'] as $col)
                    <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">{{ $col }}</span>
                @endforeach
            </div>

            @foreach ($tests as $t)
                @php
                    $pct = $t->max_possible > 0 ? round($t->avg_marks / $t->max_possible * 100) : 0;
                    $mc  = $pct >= 60 ? '#7fb685' : ($pct >= 35 ? '#d4a574' : '#c87064');
                @endphp
                <div style="display:grid;grid-template-columns:120px 1fr 100px 80px 80px 80px 80px;gap:10px;padding:13px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                     onmouseover="this.style.background='rgba(26,26,36,0.5)'" onmouseout="this.style.background=''">

                    <span style="background:#0f0f14;border-radius:4px;padding:3px 8px;font-size:11px;font-weight:500;color:#a8a39c;font-family:monospace;display:inline-block;">{{ $t->test_code }}</span>

                    <div>
                        <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $t->name }}</p>
                        <span style="font-size:10px;color:#6a665f;">{{ $t->total_questions }}Q · max {{ $t->max_possible }}pts</span>
                    </div>

                    <span style="font-size:12px;color:#a8a39c;">{{ \Carbon\Carbon::parse($t->test_date)->format('d M Y') }}</span>

                    <span style="font-size:13px;color:#a8a39c;">{{ $t->student_count }}</span>

                    <span style="font-size:14px;font-weight:700;color:{{ $mc }};">{{ $t->avg_marks }}</span>

                    <span style="font-size:13px;color:#7fb685;">{{ $t->max_marks_got }}</span>

                    <span style="font-size:13px;color:#c87064;">{{ $t->min_marks }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
