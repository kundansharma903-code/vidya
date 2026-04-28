@extends('layouts.academic-head')
@section('title', 'Teacher Assignments')
@section('breadcrumb', 'Teacher Assignments')

@section('content')
<div style="max-width:1060px;">

    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;">
        <div>
            <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Teacher Assignments</h1>
            <p style="font-size:13px;color:#a8a39c;margin:0;">Read-only view · Who teaches what, and which batches</p>
        </div>
        <span style="background:rgba(168,163,156,0.1);border:1px solid rgba(168,163,156,0.2);border-radius:6px;padding:6px 12px;font-size:11px;color:#a8a39c;">👁 View Only</span>
    </div>

    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">

        {{-- Header row --}}
        <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:200px 120px 1fr;gap:12px;padding:12px 20px;">
            <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">TEACHER</span>
            <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">SUBJECT</span>
            <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">ASSIGNED BATCHES</span>
        </div>

        @forelse ($teachers as $t)
            @php
                $asgn = $assignments[$t->id] ?? ['batches'=>[],'subjects'=>[]];
                $subjectName = $subjects->firstWhere('id', $t->primary_subject_id)?->name ?? '—';
                $subjectCode = $subjects->firstWhere('id', $t->primary_subject_id)?->code ?? '?';
                $subjectColors = ['P'=>'#7a95c8','C'=>'#d4a574','B'=>'#7fb685','Z'=>'#c87064','M'=>'#a8a39c'];
                $sColor = $subjectColors[$subjectCode] ?? '#a8a39c';
            @endphp
            <div style="display:grid;grid-template-columns:200px 120px 1fr;gap:12px;padding:14px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                 onmouseover="this.style.background='rgba(26,26,36,0.4)'" onmouseout="this.style.background=''">

                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:32px;height:32px;border-radius:50%;background:rgba(122,149,200,0.15);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#7a95c8;flex-shrink:0;">
                        {{ strtoupper(substr($t->name,0,1)) }}
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0;">{{ $t->name }}</p>
                        <span style="font-size:10px;color:#6a665f;">{{ $t->email }}</span>
                    </div>
                </div>

                <span style="background:rgba({{ implode(',',sscanf($sColor,'#%02x%02x%02x')) }},0.12);border-radius:4px;padding:3px 8px;font-size:11px;font-weight:600;color:{{ $sColor }};display:inline-block;">{{ $subjectName }}</span>

                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                    @forelse ($batches->whereIn('id', $asgn['batches']) as $b)
                        <span style="background:#0f0f14;border:1px solid rgba(245,241,232,0.08);border-radius:4px;padding:3px 8px;font-size:11px;color:#a8a39c;">{{ $b->name }}</span>
                    @empty
                        <span style="font-size:11px;color:#6a665f;">No batches assigned</span>
                    @endforelse
                </div>

            </div>
        @empty
            <div style="padding:48px;text-align:center;"><p style="color:#6a665f;font-size:14px;margin:0;">No teachers found.</p></div>
        @endforelse
    </div>

    <p style="font-size:11px;color:#6a665f;margin-top:10px;">To modify assignments, contact the institute admin.</p>
</div>
@endsection
