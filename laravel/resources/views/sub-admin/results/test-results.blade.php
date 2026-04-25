@extends('layouts.sub-admin')
@section('title', 'Test Results — ' . $test->test_code)
@section('breadcrumb', 'Test Results')

@section('content')
<div style="max-width:1080px;">

    {{-- Header --}}
    <div style="margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
            <a href="{{ route('sub-admin.tests.index') }}" style="font-size:13px;color:#6a665f;text-decoration:none;">← All Tests</a>
        </div>
        <div style="display:flex;align-items:flex-start;justify-content:space-between;">
            <div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
                    <span style="background:#0f0f14;border-radius:4px;padding:3px 8px;font-size:11px;font-weight:500;color:#a8a39c;font-family:monospace;">{{ $test->test_code }}</span>
                    <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0;">{{ $test->name }}</h1>
                    <span style="background:rgba(127,182,133,0.12);border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:500;color:#7fb685;">Analyzed</span>
                </div>
                <p style="font-size:13px;color:#a8a39c;margin:0;">{{ \Carbon\Carbon::parse($test->test_date)->format('d M Y') }} · {{ $test->total_questions }} questions · {{ $totalStudents }} students</p>
            </div>
            <a href="{{ route('sub-admin.results.upload') }}"
               style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:9px 14px;font-size:12px;font-weight:500;color:#a8a39c;text-decoration:none;">
                Re-upload OMR
            </a>
        </div>
    </div>

    {{-- Stats strip --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;">
        @php
            $avg = round($stats->avg ?? 0, 1);
            $max = $stats->max ?? 0;
            $min = $stats->min ?? 0;
        @endphp
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 4px;">Highest</p>
            <p style="font-size:24px;font-weight:700;color:#7fb685;letter-spacing:-0.48px;margin:0;">{{ $max }}</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 4px;">Average</p>
            <p style="font-size:24px;font-weight:700;color:#7a95c8;letter-spacing:-0.48px;margin:0;">{{ $avg }}</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 4px;">Lowest</p>
            <p style="font-size:24px;font-weight:700;color:#c87064;letter-spacing:-0.48px;margin:0;">{{ $min }}</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 4px;">Students</p>
            <p style="font-size:24px;font-weight:700;color:#f5f1e8;letter-spacing:-0.48px;margin:0;">{{ $totalStudents }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('sub-admin.tests.results', $test->id) }}"
          style="display:flex;gap:10px;margin-bottom:18px;align-items:center;">
        <div style="flex:1;position:relative;">
            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:13px;color:#6a665f;">🔍</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or roll number…"
                   style="width:100%;box-sizing:border-box;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:6px;padding:10px 14px 10px 34px;font-size:13px;color:#f5f1e8;outline:none;"
                   onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.08)'">
        </div>
        @if ($batches->count() > 1)
            <select name="batch_id" onchange="this.form.submit()"
                    style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;cursor:pointer;min-width:160px;appearance:none;">
                <option value="">All Batches</option>
                @foreach ($batches as $b)
                    <option value="{{ $b->id }}" {{ $batchFilter == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>
        @endif
        <button type="submit" style="background:#14141b;border:1px solid rgba(245,241,232,0.12);border-radius:6px;padding:10px 16px;font-size:13px;color:#a8a39c;cursor:pointer;">Search</button>
        @if ($search || $batchFilter)
            <a href="{{ route('sub-admin.tests.results', $test->id) }}" style="font-size:12px;color:#6a665f;text-decoration:none;padding:10px 4px;">Clear ✕</a>
        @endif
    </form>

    {{-- Results table --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;overflow:hidden;">
        <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:56px 1fr 120px 80px 80px 80px 80px 80px 80px;gap:10px;padding:11px 20px;align-items:center;">
            @foreach(['RANK','STUDENT','BATCH','MARKS','CORRECT','WRONG','SKIP','%ILE','ACTION'] as $col)
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">{{ $col }}</span>
            @endforeach
        </div>

        @forelse ($results as $r)
            @php
                $marksColor = $r->total_marks >= 120 ? '#7fb685' : ($r->total_marks >= 60 ? '#f5f1e8' : ($r->total_marks >= 0 ? '#d4a574' : '#c87064'));
                $rankBg     = $r->rank_in_batch <= 3 ? 'rgba(127,182,133,0.15)' : 'transparent';
            @endphp
            <div style="display:grid;grid-template-columns:56px 1fr 120px 80px 80px 80px 80px 80px 80px;gap:10px;padding:12px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                 onmouseover="this.style.background='rgba(26,26,36,0.5)'" onmouseout="this.style.background=''">

                {{-- Rank --}}
                <div style="display:flex;align-items:center;justify-content:center;">
                    <span style="background:{{ $rankBg }};border-radius:6px;width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:{{ $r->rank_in_batch <= 3 ? '#7fb685' : '#6a665f' }};">
                        {{ $r->rank_in_batch }}
                    </span>
                </div>

                {{-- Student --}}
                <div style="min-width:0;">
                    <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $r->student_name }}</p>
                    <span style="font-size:10px;color:#6a665f;font-family:monospace;">{{ $r->roll_number }}</span>
                </div>

                {{-- Batch --}}
                <div>
                    <span style="font-size:11px;color:#a8a39c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">{{ $r->batch_name }}</span>
                </div>

                {{-- Marks --}}
                <div>
                    <span style="font-size:14px;font-weight:700;color:{{ $marksColor }};">{{ $r->total_marks }}</span>
                </div>

                {{-- Correct --}}
                <div><span style="font-size:13px;color:#7fb685;">{{ $r->total_correct }}</span></div>

                {{-- Wrong --}}
                <div><span style="font-size:13px;color:#c87064;">{{ $r->total_incorrect }}</span></div>

                {{-- Skipped --}}
                <div><span style="font-size:13px;color:#6a665f;">{{ $r->total_unattempted }}</span></div>

                {{-- Percentile --}}
                <div><span style="font-size:12px;color:#a8a39c;">{{ $r->percentile }}%</span></div>

                {{-- Action --}}
                <div>
                    <a href="{{ route('sub-admin.tests.student-result', [$test->id, $r->student_id]) }}"
                       style="font-size:11px;font-weight:600;color:#7a95c8;text-decoration:none;background:rgba(122,149,200,0.1);border-radius:4px;padding:4px 8px;">
                        Detail →
                    </a>
                </div>
            </div>
        @empty
            <div style="padding:48px 20px;text-align:center;">
                <p style="font-size:14px;color:#6a665f;margin:0;">No results match your filters.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($results->hasPages())
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;">
            <span style="font-size:12px;color:#6a665f;">Showing {{ $results->firstItem() }}–{{ $results->lastItem() }} of {{ $results->total() }} students</span>
            <div style="display:flex;gap:6px;">
                @if ($results->onFirstPage())
                    <span style="background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:6px;padding:7px 12px;font-size:12px;color:#6a665f;">← Prev</span>
                @else
                    <a href="{{ $results->previousPageUrl() }}" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:7px 12px;font-size:12px;color:#a8a39c;text-decoration:none;">← Prev</a>
                @endif
                @if ($results->hasMorePages())
                    <a href="{{ $results->nextPageUrl() }}" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:7px 12px;font-size:12px;color:#a8a39c;text-decoration:none;">Next →</a>
                @else
                    <span style="background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:6px;padding:7px 12px;font-size:12px;color:#6a665f;">Next →</span>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection
