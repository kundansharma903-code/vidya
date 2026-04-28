@extends('layouts.reception')
@section('title', 'Search Students')
@section('breadcrumb', 'Search Students')

@section('content')
<div style="max-width:1040px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Search Students</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Find any student by name or roll number</p>
    </div>

    {{-- Search + filters --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        <input name="search" value="{{ $search }}" placeholder="Roll number or student name…" autofocus
            style="flex:1;min-width:220px;background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:8px;padding:10px 14px;color:#f5f1e8;font-size:14px;outline:none;"
            onfocus="this.style.borderColor='#c87064'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'">
        <select name="batch_id" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:8px;padding:10px 14px;color:#f5f1e8;font-size:13px;outline:none;">
            <option value="">All Batches</option>
            @foreach($batches as $b)
            <option value="{{ $b->id }}" {{ $batchFilter == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
        <select name="sort" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:8px;padding:10px 14px;color:#f5f1e8;font-size:13px;outline:none;">
            <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Sort: Name</option>
            <option value="roll" {{ $sortBy === 'roll' ? 'selected' : '' }}>Sort: Roll No</option>
        </select>
        <button type="submit" style="background:#c87064;border:none;border-radius:8px;padding:10px 20px;color:#fff;font-size:13px;font-weight:700;cursor:pointer;">Search</button>
        @if($search || $batchFilter)
        <a href="{{ route('reception.students') }}" style="background:transparent;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:10px 16px;color:#6a665f;font-size:13px;text-decoration:none;display:flex;align-items:center;">Clear</a>
        @endif
    </form>

    @if($students->total() > 0)
    <p style="font-size:12px;color:#6a665f;margin-bottom:12px;">{{ $students->total() }} student(s) found{{ $search ? ' for "'.e($search).'"' : '' }}</p>
    @endif

    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid rgba(245,241,232,0.08);">
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 22px;font-weight:700;">Roll No</th>
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Student Name</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Batch</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Latest Test</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Score</th>
                    <th style="text-align:right;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 22px;font-weight:700;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $s)
                @php
                    $latest = $latestScores->get($s->id);
                    $maxM   = $latest ? $latest->total_questions * 4 : 0;
                    $pct    = ($latest && $maxM > 0) ? (int)round($latest->total_marks / $maxM * 100) : null;
                    $pc     = $pct !== null ? ($pct >= 60 ? '#7fb685' : ($pct >= 35 ? '#d4a574' : '#c87064')) : '#6a665f';
                @endphp
                <tr style="border-bottom:1px solid rgba(245,241,232,0.04);">
                    <td style="padding:12px 22px;font-size:12px;color:#6a665f;font-family:monospace;">{{ $s->roll_number }}</td>
                    <td style="padding:12px 10px;">
                        <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">{{ $s->name }}</p>
                    </td>
                    <td style="text-align:center;padding:12px 10px;">
                        <span style="font-size:10px;color:#c87064;background:rgba(200,112,100,0.1);border-radius:4px;padding:3px 8px;font-weight:600;">{{ $s->batch_code }}</span>
                    </td>
                    <td style="text-align:center;padding:12px 10px;font-size:11px;color:#6a665f;">
                        {{ $latest ? $latest->test_code : '—' }}
                    </td>
                    <td style="text-align:center;padding:12px 10px;font-size:14px;font-weight:700;color:{{ $pc }};">
                        {{ $latest ? $latest->total_marks : '—' }}
                    </td>
                    <td style="text-align:right;padding:12px 22px;">
                        @if($latest)
                        <a href="{{ route('reception.student-result', [$s->id, $latest->test_id ?? 0]) }}"
                           style="font-size:11px;font-weight:600;color:#c87064;text-decoration:none;background:rgba(200,112,100,0.1);border-radius:5px;padding:5px 12px;border:1px solid rgba(200,112,100,0.2);">
                            View Result
                        </a>
                        @else
                        <span style="font-size:11px;color:#4a4740;">No results</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding:48px;text-align:center;color:#6a665f;font-size:14px;">
                        {{ $search ? 'No students found for "'.e($search).'".' : 'Use the search bar above to find students.' }}
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center;">
        {{ $students->links() }}
    </div>
    @endif
</div>
@endsection
