@extends('layouts.teacher')
@section('title', 'My Students')
@section('breadcrumb', 'My Students')

@section('content')
<div style="max-width:1040px;">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:22px;">
        <div>
            <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">My Students</h1>
            <p style="font-size:13px;color:#a8a39c;margin:0;">{{ $subject?->name ?? 'Subject' }} performance across all your batches</p>
        </div>
    </div>

    {{-- Search + Filter --}}
    <form method="GET" action="{{ route('teacher.students') }}" style="display:flex;gap:10px;margin-bottom:18px;align-items:center;">
        <div style="flex:1;position:relative;">
            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:13px;color:#6a665f;">🔍</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or roll number…"
                   style="width:100%;box-sizing:border-box;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:6px;padding:10px 14px 10px 34px;font-size:13px;color:#f5f1e8;outline:none;"
                   onfocus="this.style.borderColor='rgba(127,182,133,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.08)'">
        </div>
        @if ($batches->count() > 1)
            <select name="batch_id" onchange="this.form.submit()"
                    style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;cursor:pointer;min-width:160px;">
                <option value="">All Batches</option>
                @foreach ($batches as $b)
                    <option value="{{ $b->id }}" {{ $batchFilter == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        @endif
        <button type="submit" style="background:#14141b;border:1px solid rgba(245,241,232,0.12);border-radius:6px;padding:10px 16px;font-size:13px;color:#a8a39c;cursor:pointer;">Search</button>
        @if ($search || $batchFilter)
            <a href="{{ route('teacher.students') }}" style="font-size:12px;color:#6a665f;text-decoration:none;padding:10px 4px;">Clear ✕</a>
        @endif
    </form>

    {{-- Table --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;overflow:hidden;">

        <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:110px 1fr 140px 80px 120px 80px 90px;gap:10px;padding:11px 20px;align-items:center;">
            @foreach(['ROLL','NAME','BATCH','MEDIUM','MASTERY','TOPICS','ACTION'] as $col)
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">{{ $col }}</span>
            @endforeach
        </div>

        @forelse ($studentRows as $s)
            @php
                $m = $masteryMap->get($s->id);
                $avgM = $m ? (int) round($m->avg_m) : null;
                $mc = $avgM === null ? '#6a665f' : ($avgM >= 70 ? '#7fb685' : ($avgM >= 40 ? '#d4a574' : '#c87064'));
                $rk = $rankMap->get($s->id);
            @endphp
            <div style="display:grid;grid-template-columns:110px 1fr 140px 80px 120px 80px 90px;gap:10px;padding:12px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                 onmouseover="this.style.background='rgba(26,26,36,0.5)'" onmouseout="this.style.background=''">

                <span style="font-size:11px;color:#6a665f;font-family:monospace;">{{ $s->roll_number }}</span>

                <div style="min-width:0;">
                    <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s->name }}</p>
                    @if ($rk && $lastTest)
                        <span style="font-size:10px;color:#6a665f;">Rank #{{ $rk->rank_in_batch }} · {{ $lastTest->test_code }}</span>
                    @endif
                </div>

                <span style="font-size:11px;color:#a8a39c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">{{ $s->batch_name }}</span>

                <span style="font-size:11px;color:#6a665f;">{{ ucfirst($s->medium) }}</span>

                <div>
                    @if ($avgM !== null)
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="flex:1;height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                                <div style="height:100%;background:{{ $mc }};width:{{ $avgM }}%;"></div>
                            </div>
                            <span style="font-size:12px;font-weight:600;color:{{ $mc }};min-width:32px;text-align:right;">{{ $avgM }}%</span>
                        </div>
                    @else
                        <span style="font-size:11px;color:#6a665f;">No data</span>
                    @endif
                </div>

                <span style="font-size:12px;color:#a8a39c;">{{ $m ? $m->topics_count : '–' }}</span>

                <a href="{{ route('teacher.students.detail', $s->id) }}"
                   style="font-size:11px;font-weight:600;color:#7fb685;text-decoration:none;background:rgba(127,182,133,0.1);border-radius:4px;padding:4px 8px;">
                    View →
                </a>
            </div>
        @empty
            <div style="padding:48px 20px;text-align:center;">
                <p style="font-size:14px;color:#6a665f;margin:0;">No students found{{ $search ? ' matching your search' : '' }}.</p>
            </div>
        @endforelse
    </div>

    <p style="font-size:12px;color:#6a665f;margin-top:12px;">{{ $studentRows->count() }} students shown</p>
</div>
@endsection
