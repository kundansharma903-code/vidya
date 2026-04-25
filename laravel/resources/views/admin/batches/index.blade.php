@extends('layouts.admin')

@section('title', 'Batches')
@section('breadcrumb', 'Batches')

@section('content')

{{-- Flash --}}
@if (session('success'))
<div style="background:rgba(127,182,133,0.12);border:1px solid rgba(127,182,133,0.3);border-radius:6px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#7fb685;">
    {{ session('success') }}
</div>
@endif

{{-- Page Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px 0;">Batches</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
            <span style="font-weight:500;color:#f5f1e8;">{{ $stats->total ?? 0 }} total</span>
            <span style="color:#6a665f;">·</span>
            <span style="color:#7fb685;">{{ $stats->active_count ?? 0 }} active</span>
            @if ($courseCount > 0)
                <span style="color:#6a665f;">·</span>
                <span style="color:#a8a39c;">{{ $courseCount }} {{ Str::plural('course', $courseCount) }}</span>
            @endif
            @if ($avgStudents > 0)
                <span style="color:#6a665f;">·</span>
                <span style="color:#a8a39c;">avg {{ $avgStudents }} students</span>
            @endif
        </div>
    </div>
    <button onclick="document.getElementById('addModal').style.display='flex'"
            style="display:flex;align-items:center;gap:6px;background:#7a95c8;color:#14141b;border:none;border-radius:6px;padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 6px rgba(122,149,200,0.2);">
        <span style="font-size:16px;font-weight:700;line-height:1;">+</span>
        Add Batch
    </button>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.batches') }}" style="display:flex;gap:12px;margin-bottom:20px;">
    <div style="flex:1;position:relative;">
        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#6a665f;font-size:14px;pointer-events:none;">⌕</span>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by batch name or code…"
               style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px 11px 36px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    </div>
    <select name="course_id"
            style="background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px;font-size:13px;color:#a8a39c;width:160px;outline:none;cursor:pointer;"
            onchange="this.form.submit()">
        <option value="">All Courses</option>
        @foreach ($courses as $course)
            <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
        @endforeach
    </select>
    <select name="status"
            style="background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px;font-size:13px;color:#a8a39c;width:140px;outline:none;cursor:pointer;"
            onchange="this.form.submit()">
        <option value="">All Status</option>
        <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
</form>

{{-- Table --}}
<div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;overflow:hidden;margin-bottom:20px;">

    {{-- Header --}}
    <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:flex;align-items:center;gap:12px;height:44px;padding:0 20px;">
        <div style="width:24px;flex-shrink:0;"></div>
        <div style="flex:1;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Batch</div>
        <div style="width:150px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Course</div>
        <div style="width:120px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Timing</div>
        <div style="width:170px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Capacity</div>
        <div style="width:110px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Teachers</div>
        <div style="width:90px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Status</div>
        <div style="width:32px;flex-shrink:0;"></div>
    </div>

    {{-- Rows --}}
    @forelse ($batches as $batch)
    @php
        $enrolled  = $batch->student_count ?? 0;
        $capacity  = $batch->capacity ?? 1;
        $pct       = $capacity > 0 ? round(($enrolled / $capacity) * 100) : 0;
        $barColor  = $pct >= 90 ? '#c87064' : ($pct >= 75 ? '#d4a574' : '#7fb685');
        $pctColor  = $barColor;
        $statusColor = $batch->is_active ? '#7fb685' : '#a8a39c';
        $statusBg    = $batch->is_active ? 'rgba(127,182,133,0.12)' : 'rgba(168,163,156,0.12)';
        $statusLabel = $batch->is_active ? 'Active' : 'Inactive';
    @endphp
    <div style="display:flex;align-items:center;gap:12px;height:60px;padding:0 20px;border-bottom:1px solid rgba(245,241,232,0.05);"
         onmouseover="this.style.background='rgba(26,26,36,0.4)'"
         onmouseout="this.style.background=''">

        {{-- Checkbox --}}
        <div style="width:24px;flex-shrink:0;display:flex;align-items:center;">
            <input type="checkbox" style="width:16px;height:16px;accent-color:#7a95c8;cursor:pointer;">
        </div>

        {{-- Batch name + code --}}
        <div style="flex:1;min-width:0;">
            <div style="font-size:14px;font-weight:500;color:#f5f1e8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $batch->name }}</div>
            <div style="font-size:11px;color:#6a665f;margin-top:2px;">{{ $batch->code ?? '—' }}</div>
        </div>

        {{-- Course --}}
        <div style="width:150px;flex-shrink:0;">
            <span style="font-size:13px;color:#a8a39c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;">{{ $batch->course_name }}</span>
        </div>

        {{-- Timing --}}
        <div style="width:120px;flex-shrink:0;">
            <span style="font-size:13px;color:#a8a39c;">{{ $batch->timing_label ?? '—' }}</span>
        </div>

        {{-- Capacity bar --}}
        <div style="width:170px;flex-shrink:0;">
            <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                <span style="font-size:13px;font-weight:500;color:#f5f1e8;">{{ $enrolled }} / {{ $capacity }}</span>
                <span style="font-size:11px;color:{{ $pctColor }};">{{ $pct }}%</span>
            </div>
            <div style="height:4px;background:rgba(245,241,232,0.08);border-radius:2px;width:140px;overflow:hidden;">
                <div style="height:100%;width:{{ min($pct, 100) }}%;background:{{ $barColor }};border-radius:2px;transition:width 0.3s;"></div>
            </div>
        </div>

        {{-- Teachers --}}
        <div style="width:110px;flex-shrink:0;">
            <span style="font-size:13px;color:#a8a39c;">{{ $batch->teacher_count }} assigned</span>
        </div>

        {{-- Status --}}
        <div style="width:90px;flex-shrink:0;">
            <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $statusBg }};border-radius:9999px;padding:4px 10px 4px 8px;">
                <span style="width:6px;height:6px;background:{{ $statusColor }};border-radius:50%;flex-shrink:0;"></span>
                <span style="font-size:11px;font-weight:500;color:{{ $statusColor }};">{{ $statusLabel }}</span>
            </span>
        </div>

        {{-- Menu --}}
        <div style="width:32px;flex-shrink:0;display:flex;align-items:center;justify-content:center;position:relative;">
            <button onclick="toggleMenu({{ $batch->id }})"
                    style="width:28px;height:28px;background:none;border:none;border-radius:4px;cursor:pointer;color:#6a665f;font-size:14px;font-weight:700;display:flex;align-items:center;justify-content:center;"
                    onmouseover="this.style.background='rgba(245,241,232,0.06)'"
                    onmouseout="this.style.background='none'">•••</button>
            <div id="menu-{{ $batch->id }}" style="display:none;position:absolute;right:0;top:32px;background:#1a1a24;border:1px solid rgba(245,241,232,0.1);border-radius:6px;min-width:140px;z-index:100;box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                <button onclick="openEditModal({{ json_encode($batch) }})"
                        style="width:100%;text-align:left;background:none;border:none;padding:10px 14px;font-size:13px;color:#a8a39c;cursor:pointer;"
                        onmouseover="this.style.color='#f5f1e8';this.style.background='rgba(245,241,232,0.04)'"
                        onmouseout="this.style.color='#a8a39c';this.style.background='none'">Edit</button>
                <form method="POST" action="{{ route('admin.batches.toggle', $batch->id) }}" style="margin:0;">
                    @csrf @method('PATCH')
                    <button type="submit"
                            style="width:100%;text-align:left;background:none;border:none;padding:10px 14px;font-size:13px;color:#a8a39c;cursor:pointer;"
                            onmouseover="this.style.color='#f5f1e8';this.style.background='rgba(245,241,232,0.04)'"
                            onmouseout="this.style.color='#a8a39c';this.style.background='none'">
                        {{ $batch->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.batches.destroy', $batch->id) }}" style="margin:0;"
                      onsubmit="return confirm('Delete this batch? Students in this batch will also be affected.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            style="width:100%;text-align:left;background:none;border:none;padding:10px 14px;font-size:13px;color:#e05252;cursor:pointer;border-top:1px solid rgba(245,241,232,0.06);"
                            onmouseover="this.style.background='rgba(224,82,82,0.08)'"
                            onmouseout="this.style.background='none'">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:64px 0;">
        <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="rgba(245,241,232,0.12)" stroke-width="1.25" style="margin-bottom:12px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        <p style="font-size:14px;color:#6a665f;margin:0 0 4px 0;">No batches yet</p>
        <p style="font-size:12px;color:#4a4640;margin:0;">
            @if ($courses->isEmpty())
                Create a Course first, then add batches.
            @else
                Click "Add Batch" to create your first batch.
            @endif
        </p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div style="display:flex;align-items:center;justify-content:space-between;">
    <span style="font-size:13px;color:#6a665f;">
        Showing {{ $batches->firstItem() ?? 0 }}–{{ $batches->lastItem() ?? 0 }} of {{ $batches->total() }} {{ Str::plural('batch', $batches->total()) }}
    </span>
    @if ($batches->hasPages())
    <div style="display:flex;gap:4px;align-items:center;">
        @if ($batches->onFirstPage())
            <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#4a4640;border-radius:6px;">‹</span>
        @else
            <a href="{{ $batches->previousPageUrl() }}" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#6a665f;border-radius:6px;text-decoration:none;border:1px solid rgba(245,241,232,0.08);">‹</a>
        @endif
        @foreach ($batches->getUrlRange(1, $batches->lastPage()) as $page => $url)
            @if ($page === $batches->currentPage())
                <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#14141b;background:#7a95c8;border-radius:6px;">{{ $page }}</span>
            @else
                <a href="{{ $url }}" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#a8a39c;border-radius:6px;text-decoration:none;border:1px solid rgba(245,241,232,0.1);">{{ $page }}</a>
            @endif
        @endforeach
        @if ($batches->hasMorePages())
            <a href="{{ $batches->nextPageUrl() }}" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#6a665f;border-radius:6px;text-decoration:none;border:1px solid rgba(245,241,232,0.08);">›</a>
        @else
            <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#4a4640;border-radius:6px;">›</span>
        @endif
    </div>
    @else
    <span style="font-size:13px;color:#6a665f;">Page 1 of 1</span>
    @endif
</div>

{{-- Add Modal --}}
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.75);z-index:200;align-items:center;justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:10px;width:480px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.06);">
            <span style="font-size:16px;font-weight:600;color:#f5f1e8;">Add Batch</span>
            <button onclick="document.getElementById('addModal').style.display='none'"
                    style="background:none;border:none;cursor:pointer;color:#6a665f;font-size:20px;line-height:1;padding:0;">×</button>
        </div>
        <form method="POST" action="{{ route('admin.batches.store') }}" style="padding:24px;display:flex;flex-direction:column;gap:18px;">
            @csrf
            @include('admin.batches._form', ['courses' => $courses])
            <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid rgba(245,241,232,0.06);">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                        style="background:none;border:1px solid rgba(245,241,232,0.12);border-radius:6px;padding:9px 18px;font-size:13px;color:#a8a39c;cursor:pointer;">Cancel</button>
                <button type="submit"
                        style="background:#7a95c8;border:none;border-radius:6px;padding:9px 18px;font-size:13px;font-weight:600;color:#14141b;cursor:pointer;">Create Batch</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.75);z-index:200;align-items:center;justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:10px;width:480px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.06);">
            <span style="font-size:16px;font-weight:600;color:#f5f1e8;">Edit Batch</span>
            <button onclick="document.getElementById('editModal').style.display='none'"
                    style="background:none;border:none;cursor:pointer;color:#6a665f;font-size:20px;line-height:1;padding:0;">×</button>
        </div>
        <form id="editForm" method="POST" style="padding:24px;display:flex;flex-direction:column;gap:18px;">
            @csrf @method('PUT')
            @include('admin.batches._form', ['courses' => $courses, 'edit' => true])
            <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid rgba(245,241,232,0.06);">
                <button type="button" onclick="document.getElementById('editModal').style.display='none'"
                        style="background:none;border:1px solid rgba(245,241,232,0.12);border-radius:6px;padding:9px 18px;font-size:13px;color:#a8a39c;cursor:pointer;">Cancel</button>
                <button type="submit"
                        style="background:#7a95c8;border:none;border-radius:6px;padding:9px 18px;font-size:13px;font-weight:600;color:#14141b;cursor:pointer;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMenu(id) {
    document.querySelectorAll('[id^="menu-"]').forEach(m => {
        if (m.id !== 'menu-' + id) m.style.display = 'none';
    });
    const menu = document.getElementById('menu-' + id);
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}
document.addEventListener('click', function(e) {
    if (!e.target.closest('[id^="menu-"]') && !e.target.closest('button[onclick^="toggleMenu"]')) {
        document.querySelectorAll('[id^="menu-"]').forEach(m => m.style.display = 'none');
    }
});
function openEditModal(batch) {
    document.querySelectorAll('[id^="menu-"]').forEach(m => m.style.display = 'none');
    const form = document.getElementById('editForm');
    form.action = '/admin/batches/' + batch.id;
    form.querySelector('[name="name"]').value         = batch.name;
    form.querySelector('[name="code"]').value         = batch.code ?? '';
    form.querySelector('[name="course_id"]').value    = batch.course_id;
    form.querySelector('[name="timing_label"]').value = batch.timing_label ?? '';
    form.querySelector('[name="capacity"]').value     = batch.capacity;
    form.querySelector('[name="start_date"]').value   = batch.start_date ?? '';
    form.querySelector('[name="end_date"]').value     = batch.end_date ?? '';
    document.getElementById('editModal').style.display = 'flex';
}
@if ($errors->any())
    document.getElementById('addModal').style.display = 'flex';
@endif
</script>

@endsection
