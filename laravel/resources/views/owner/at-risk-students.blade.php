@extends('layouts.owner')
@section('title', 'At-Risk Students')
@section('breadcrumb', 'At-Risk Students')

@section('content')
<div style="max-width:1000px;">
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">At-Risk Students</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Students with average mastery below 40% — sorted lowest first</p>
    </div>

    {{-- Filters --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:18px;flex-wrap:wrap;">
        <input name="search" value="{{ $search }}" placeholder="Search name / roll..." style="flex:1;min-width:180px;background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:8px 12px;color:#f5f1e8;font-size:13px;outline:none;">
        <select name="batch_id" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:8px 12px;color:#f5f1e8;font-size:13px;outline:none;">
            <option value="">All Batches</option>
            @foreach($batches as $b)
            <option value="{{ $b->id }}" {{ $batchFilter == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
        <select name="subject_id" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:8px 12px;color:#f5f1e8;font-size:13px;outline:none;">
            <option value="">All Subjects</option>
            @foreach($subjects as $s)
            <option value="{{ $s->id }}" {{ $subjectFilter == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
            @endforeach
        </select>
        <button type="submit" style="background:rgba(163,146,200,0.15);border:1px solid rgba(163,146,200,0.3);border-radius:6px;padding:8px 16px;color:#a392c8;font-size:13px;font-weight:600;cursor:pointer;">Filter</button>
        @if($search || $batchFilter || $subjectFilter)
        <a href="{{ route('owner.at-risk-students') }}" style="background:transparent;border:1px solid rgba(245,241,232,0.08);border-radius:6px;padding:8px 16px;color:#6a665f;font-size:13px;text-decoration:none;display:flex;align-items:center;">Clear</a>
        @endif
    </form>

    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid rgba(245,241,232,0.08);">
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 22px;font-weight:700;">#</th>
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 10px;font-weight:700;">Student</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 10px;font-weight:700;">Batch</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 10px;font-weight:700;">Topics Attempted</th>
                    <th style="text-align:right;font-size:9px;color:#c87064;text-transform:uppercase;letter-spacing:1px;padding:14px 22px;font-weight:700;">Avg Mastery</th>
                </tr>
            </thead>
            <tbody>
                @forelse($atRisk as $i => $s)
                @php $mc = $s->avg_m >= 30 ? '#d4a574' : '#c87064'; @endphp
                <tr style="border-bottom:1px solid rgba(245,241,232,0.04);">
                    <td style="padding:12px 22px;font-size:12px;color:#6a665f;">{{ $i + 1 }}</td>
                    <td style="padding:12px 10px;">
                        <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">{{ $s->name }}</p>
                        <p style="font-size:10px;color:#6a665f;margin:0;">{{ $s->roll_number }}</p>
                    </td>
                    <td style="text-align:center;padding:12px 10px;font-size:12px;color:#a8a39c;">{{ $s->batch_name }}</td>
                    <td style="text-align:center;padding:12px 10px;font-size:12px;color:#6a665f;">{{ $s->topics }}</td>
                    <td style="text-align:right;padding:12px 22px;">
                        <span style="font-size:16px;font-weight:800;color:{{ $mc }};">{{ $s->avg_m }}%</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:#7fb685;font-size:14px;">
                        No at-risk students found with current filters.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($atRisk->count() > 0)
    <p style="font-size:11px;color:#6a665f;margin:10px 0 0;text-align:right;">{{ $atRisk->count() }} student(s) at risk</p>
    @endif
</div>
@endsection
