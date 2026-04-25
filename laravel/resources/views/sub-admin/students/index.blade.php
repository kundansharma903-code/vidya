@extends('layouts.sub-admin')
@section('title', 'Students')
@section('breadcrumb', 'Students')

@section('content')

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px;">Students</h1>
            <p style="font-size:14px;color:#a8a39c;margin:0;">Read-only view · {{ $total }} total · {{ $active }} active</p>
        </div>
        <div style="background:rgba(122,149,200,0.08);border:1px solid rgba(122,149,200,0.2);border-radius:6px;padding:7px 12px;font-size:11px;color:#7a95c8;font-weight:500;">
            👁 View Only
        </div>
    </div>

    {{-- KPI strip --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 6px;">Total Students</p>
            <p style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 6px;">{{ $total }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Enrolled in institute</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 6px;">Active</p>
            <p style="font-size:26px;font-weight:700;color:#7fb685;letter-spacing:-0.52px;margin:0 0 6px;">{{ $active }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Currently active</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:18px 20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 6px;">Batches</p>
            <p style="font-size:26px;font-weight:700;color:#7a95c8;letter-spacing:-0.52px;margin:0 0 6px;">{{ $batches->count() }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Active batches</p>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('sub-admin.students') }}"
          style="display:flex;gap:10px;margin-bottom:20px;align-items:center;">

        <div style="flex:1;position:relative;">
            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:13px;color:#6a665f;">🔍</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, roll number or email…"
                   style="width:100%;box-sizing:border-box;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:6px;padding:10px 14px 10px 34px;font-size:13px;color:#f5f1e8;outline:none;"
                   onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.08)'">
        </div>

        <select name="batch_id"
                style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;cursor:pointer;min-width:180px;appearance:none;"
                onchange="this.form.submit()">
            <option value="">All Batches</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" {{ $batchFilter == $batch->id ? 'selected' : '' }}>
                    {{ $batch->name }}
                </option>
            @endforeach
        </select>

        <button type="submit"
                style="background:#14141b;border:1px solid rgba(245,241,232,0.12);border-radius:6px;padding:10px 16px;font-size:13px;color:#a8a39c;cursor:pointer;">
            Search
        </button>

        @if ($search || $batchFilter)
            <a href="{{ route('sub-admin.students') }}"
               style="font-size:12px;color:#6a665f;text-decoration:none;padding:10px 12px;white-space:nowrap;">
                Clear ✕
            </a>
        @endif

    </form>

    {{-- Table --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;overflow:hidden;">

        {{-- Header --}}
        <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:120px 1fr 180px 160px 80px 80px;gap:12px;padding:12px 20px;align-items:center;">
            @foreach(['ROLL NO','NAME','BATCH','COURSE','MEDIUM','STATUS'] as $col)
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;">{{ $col }}</span>
            @endforeach
        </div>

        {{-- Rows --}}
        @forelse ($students as $student)
            <div style="display:grid;grid-template-columns:120px 1fr 180px 160px 80px 80px;gap:12px;padding:12px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                 onmouseover="this.style.background='rgba(26,26,36,0.4)'" onmouseout="this.style.background=''">

                <div>
                    <span style="background:#0f0f14;border-radius:4px;padding:3px 8px;font-size:11px;font-weight:500;color:#a8a39c;font-family:monospace;">
                        {{ $student->roll_number }}
                    </span>
                </div>

                <div>
                    <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        {{ $student->name }}
                    </p>
                    @if ($student->email)
                        <p style="font-size:11px;color:#6a665f;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $student->email }}</p>
                    @endif
                </div>

                <div>
                    <span style="font-size:12px;font-weight:500;color:#a8a39c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">
                        {{ $student->batch_name }}
                    </span>
                    @if ($student->batch_code)
                        <span style="font-size:10px;color:#6a665f;">{{ $student->batch_code }}</span>
                    @endif
                </div>

                <div>
                    <span style="font-size:12px;color:#a8a39c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">
                        {{ $student->course_name ?? '—' }}
                    </span>
                </div>

                <div>
                    <span style="font-size:11px;color:#a8a39c;text-transform:capitalize;">{{ $student->medium }}</span>
                </div>

                <div>
                    @if ($student->is_active)
                        <span style="background:rgba(127,182,133,0.12);border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:500;color:#7fb685;">Active</span>
                    @else
                        <span style="background:rgba(168,163,156,0.1);border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:500;color:#6a665f;">Inactive</span>
                    @endif
                </div>

            </div>
        @empty
            <div style="padding:48px 20px;text-align:center;">
                <p style="font-size:14px;color:#6a665f;margin:0;">
                    {{ $search || $batchFilter ? 'No students match your filters.' : 'No students enrolled yet.' }}
                </p>
            </div>
        @endforelse

    </div>

    {{-- Pagination --}}
    @if ($students->hasPages())
        <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;">
            <span style="font-size:12px;color:#6a665f;">
                Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }} students
            </span>
            <div style="display:flex;gap:6px;">
                @if ($students->onFirstPage())
                    <span style="background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:6px;padding:7px 12px;font-size:12px;color:#6a665f;">← Prev</span>
                @else
                    <a href="{{ $students->previousPageUrl() }}" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:7px 12px;font-size:12px;color:#a8a39c;text-decoration:none;">← Prev</a>
                @endif
                @if ($students->hasMorePages())
                    <a href="{{ $students->nextPageUrl() }}" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:7px 12px;font-size:12px;color:#a8a39c;text-decoration:none;">Next →</a>
                @else
                    <span style="background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:6px;padding:7px 12px;font-size:12px;color:#6a665f;">Next →</span>
                @endif
            </div>
        </div>
    @endif

@endsection
