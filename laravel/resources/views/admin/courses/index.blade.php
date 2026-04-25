@extends('layouts.admin')

@section('title', 'Courses')
@section('breadcrumb', 'Courses')

@section('content')

@php
    $examLabels = [
        'NEET'         => ['label' => 'NEET',         'color' => '#7fb685', 'bg' => 'rgba(127,182,133,0.12)'],
        'JEE_MAIN'     => ['label' => 'JEE-Main',     'color' => '#a392c8', 'bg' => 'rgba(163,146,200,0.12)'],
        'JEE_ADVANCED' => ['label' => 'JEE-Advanced',  'color' => '#d4a574', 'bg' => 'rgba(212,165,116,0.12)'],
        'OTHER'        => ['label' => 'Other',         'color' => '#a8a39c', 'bg' => 'rgba(168,163,156,0.12)'],
    ];
@endphp

{{-- Flash --}}
@if (session('success'))
<div style="background:rgba(127,182,133,0.12);border:1px solid rgba(127,182,133,0.3);border-radius:6px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#7fb685;">
    {{ session('success') }}
</div>
@endif

{{-- Page Header --}}
<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
    <div>
        <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px 0;">Courses</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
            <span style="font-weight:500;color:#f5f1e8;">{{ $stats->total ?? 0 }} total</span>
            <span style="color:#6a665f;">·</span>
            <span style="color:#7fb685;">{{ $stats->active_count ?? 0 }} active</span>
            @if (($stats->neet_count ?? 0) > 0)
                <span style="color:#6a665f;">·</span>
                <span style="color:#a8a39c;">{{ $stats->neet_count }} NEET</span>
            @endif
            @if (($stats->jee_count ?? 0) > 0)
                <span style="color:#6a665f;">·</span>
                <span style="color:#a8a39c;">{{ $stats->jee_count }} JEE</span>
            @endif
        </div>
    </div>
    <button onclick="document.getElementById('addModal').style.display='flex'"
            style="display:flex;align-items:center;gap:6px;background:#7a95c8;color:#14141b;border:none;border-radius:6px;padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 6px rgba(122,149,200,0.2);">
        <span style="font-size:16px;font-weight:700;line-height:1;">+</span>
        Add Course
    </button>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.courses') }}" style="display:flex;gap:12px;margin-bottom:20px;">
    <div style="flex:1;position:relative;">
        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#6a665f;font-size:14px;pointer-events:none;">⌕</span>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by course name or code…"
               style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px 11px 36px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    </div>
    <select name="exam_type"
            style="background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px;font-size:13px;color:#a8a39c;width:170px;outline:none;cursor:pointer;"
            onchange="this.form.submit()">
        <option value="">All Exam Types</option>
        <option value="NEET"         {{ request('exam_type') === 'NEET'         ? 'selected' : '' }}>NEET</option>
        <option value="JEE_MAIN"     {{ request('exam_type') === 'JEE_MAIN'     ? 'selected' : '' }}>JEE-Main</option>
        <option value="JEE_ADVANCED" {{ request('exam_type') === 'JEE_ADVANCED' ? 'selected' : '' }}>JEE-Advanced</option>
        <option value="OTHER"        {{ request('exam_type') === 'OTHER'        ? 'selected' : '' }}>Other</option>
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
        <div style="flex:1;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Course</div>
        <div style="width:120px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Exam Type</div>
        <div style="width:150px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Structure</div>
        <div style="width:90px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Target</div>
        <div style="width:180px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Enrollment</div>
        <div style="width:90px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Status</div>
        <div style="width:32px;flex-shrink:0;"></div>
    </div>

    {{-- Rows --}}
    @forelse ($courses as $course)
    @php
        $examInfo = $examLabels[$course->exam_type] ?? $examLabels['OTHER'];
        $statusColor = $course->is_active ? '#7fb685' : '#a8a39c';
        $statusBg    = $course->is_active ? 'rgba(127,182,133,0.12)' : 'rgba(168,163,156,0.12)';
        $statusLabel = $course->is_active ? 'Active' : 'Inactive';
    @endphp
    <div style="display:flex;align-items:center;gap:12px;height:60px;padding:0 20px;border-bottom:1px solid rgba(245,241,232,0.05);"
         onmouseover="this.style.background='rgba(26,26,36,0.4)'"
         onmouseout="this.style.background=''">

        {{-- Checkbox --}}
        <div style="width:24px;flex-shrink:0;display:flex;align-items:center;">
            <input type="checkbox" style="width:16px;height:16px;accent-color:#7a95c8;cursor:pointer;background:#0f0f14;border:1px solid rgba(245,241,232,0.15);border-radius:3px;">
        </div>

        {{-- Course name + code --}}
        <div style="flex:1;min-width:0;">
            <div style="font-size:14px;font-weight:600;color:#f5f1e8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $course->name }}</div>
            <div style="font-size:11px;color:#6a665f;margin-top:2px;">{{ $course->code }} · {{ $examInfo['label'] }}</div>
        </div>

        {{-- Exam Type badge --}}
        <div style="width:120px;flex-shrink:0;">
            <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $examInfo['bg'] }};border-radius:9999px;padding:4px 10px 4px 8px;">
                <span style="width:6px;height:6px;background:{{ $examInfo['color'] }};border-radius:50%;flex-shrink:0;"></span>
                <span style="font-size:11px;font-weight:500;color:{{ $examInfo['color'] }};white-space:nowrap;">{{ $examInfo['label'] }}</span>
            </span>
        </div>

        {{-- Structure --}}
        <div style="width:150px;flex-shrink:0;">
            <div style="font-size:13px;color:#f5f1e8;">{{ $course->duration_months }} months</div>
            <div style="font-size:11px;color:#6a665f;margin-top:2px;">{{ $course->total_questions ?? '—' }}{{ $course->total_questions ? ' Q' : '' }}</div>
        </div>

        {{-- Target year --}}
        <div style="width:90px;flex-shrink:0;">
            <span style="font-size:13px;font-weight:500;color:#a8a39c;">{{ $course->target_year }}</span>
        </div>

        {{-- Enrollment --}}
        <div style="width:180px;flex-shrink:0;">
            <div style="font-size:13px;font-weight:500;color:#f5f1e8;">— students</div>
            <div style="font-size:11px;color:#6a665f;margin-top:2px;">{{ $course->batch_count }} {{ Str::plural('batch', $course->batch_count) }}</div>
        </div>

        {{-- Status badge --}}
        <div style="width:90px;flex-shrink:0;">
            <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $statusBg }};border-radius:9999px;padding:4px 10px 4px 8px;">
                <span style="width:6px;height:6px;background:{{ $statusColor }};border-radius:50%;flex-shrink:0;"></span>
                <span style="font-size:11px;font-weight:500;color:{{ $statusColor }};">{{ $statusLabel }}</span>
            </span>
        </div>

        {{-- Actions menu --}}
        <div style="width:32px;flex-shrink:0;display:flex;align-items:center;justify-content:center;position:relative;">
            <button onclick="toggleMenu({{ $course->id }})"
                    style="width:28px;height:28px;background:none;border:none;border-radius:4px;cursor:pointer;color:#6a665f;font-size:14px;font-weight:700;display:flex;align-items:center;justify-content:center;"
                    onmouseover="this.style.background='rgba(245,241,232,0.06)'"
                    onmouseout="this.style.background='none'">•••</button>
            <div id="menu-{{ $course->id }}" style="display:none;position:absolute;right:0;top:32px;background:#1a1a24;border:1px solid rgba(245,241,232,0.1);border-radius:6px;min-width:140px;z-index:100;box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                <button onclick="openEditModal({{ json_encode($course) }})"
                        style="width:100%;text-align:left;background:none;border:none;padding:10px 14px;font-size:13px;color:#a8a39c;cursor:pointer;display:block;"
                        onmouseover="this.style.color='#f5f1e8';this.style.background='rgba(245,241,232,0.04)'"
                        onmouseout="this.style.color='#a8a39c';this.style.background='none'">Edit</button>
                <form method="POST" action="{{ route('admin.courses.toggle', $course->id) }}" style="margin:0;">
                    @csrf @method('PATCH')
                    <button type="submit"
                            style="width:100%;text-align:left;background:none;border:none;padding:10px 14px;font-size:13px;color:#a8a39c;cursor:pointer;"
                            onmouseover="this.style.color='#f5f1e8';this.style.background='rgba(245,241,232,0.04)'"
                            onmouseout="this.style.color='#a8a39c';this.style.background='none'">
                        {{ $course->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.courses.destroy', $course->id) }}" style="margin:0;"
                      onsubmit="return confirm('Delete this course? This cannot be undone.')">
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
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p style="font-size:14px;color:#6a665f;margin:0 0 4px 0;">No courses yet</p>
        <p style="font-size:12px;color:#4a4640;margin:0;">Click "Add Course" to create your first course.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div style="display:flex;align-items:center;justify-content:space-between;">
    <span style="font-size:13px;color:#6a665f;">
        Showing {{ $courses->firstItem() ?? 0 }}–{{ $courses->lastItem() ?? 0 }} of {{ $courses->total() }} course{{ $courses->total() !== 1 ? 's' : '' }}
    </span>
    @if ($courses->hasPages())
    <div style="display:flex;gap:4px;align-items:center;">
        {{-- Previous --}}
        @if ($courses->onFirstPage())
            <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#4a4640;border-radius:6px;">‹</span>
        @else
            <a href="{{ $courses->previousPageUrl() }}" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#6a665f;border-radius:6px;text-decoration:none;border:1px solid rgba(245,241,232,0.08);">‹</a>
        @endif

        {{-- Page numbers --}}
        @foreach ($courses->getUrlRange(1, $courses->lastPage()) as $page => $url)
            @if ($page === $courses->currentPage())
                <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#14141b;background:#7a95c8;border-radius:6px;">{{ $page }}</span>
            @else
                <a href="{{ $url }}" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#6a665f;border-radius:6px;text-decoration:none;border:1px solid rgba(245,241,232,0.08);">{{ $page }}</a>
            @endif
        @endforeach

        {{-- Next --}}
        @if ($courses->hasMorePages())
            <a href="{{ $courses->nextPageUrl() }}" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#6a665f;border-radius:6px;text-decoration:none;border:1px solid rgba(245,241,232,0.08);">›</a>
        @else
            <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#4a4640;border-radius:6px;">›</span>
        @endif
    </div>
    @else
    <span style="font-size:13px;color:#6a665f;">Page 1 of 1</span>
    @endif
</div>

{{-- ============================================================ --}}
{{-- Add Course Modal --}}
{{-- ============================================================ --}}
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.75);z-index:200;align-items:center;justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:10px;width:480px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.06);">
            <span style="font-size:16px;font-weight:600;color:#f5f1e8;">Add Course</span>
            <button onclick="document.getElementById('addModal').style.display='none'"
                    style="background:none;border:none;cursor:pointer;color:#6a665f;font-size:20px;line-height:1;padding:0;">×</button>
        </div>
        <form method="POST" action="{{ route('admin.courses.store') }}" style="padding:24px;display:flex;flex-direction:column;gap:18px;">
            @csrf
            @include('admin.courses._form')
            <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid rgba(245,241,232,0.06);">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                        style="background:none;border:1px solid rgba(245,241,232,0.12);border-radius:6px;padding:9px 18px;font-size:13px;color:#a8a39c;cursor:pointer;">Cancel</button>
                <button type="submit"
                        style="background:#7a95c8;border:none;border-radius:6px;padding:9px 18px;font-size:13px;font-weight:600;color:#14141b;cursor:pointer;">Create Course</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Course Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.75);z-index:200;align-items:center;justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:10px;width:480px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.06);">
            <span style="font-size:16px;font-weight:600;color:#f5f1e8;">Edit Course</span>
            <button onclick="document.getElementById('editModal').style.display='none'"
                    style="background:none;border:none;cursor:pointer;color:#6a665f;font-size:20px;line-height:1;padding:0;">×</button>
        </div>
        <form id="editForm" method="POST" style="padding:24px;display:flex;flex-direction:column;gap:18px;">
            @csrf @method('PUT')
            @include('admin.courses._form', ['edit' => true])
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

function openEditModal(course) {
    document.querySelectorAll('[id^="menu-"]').forEach(m => m.style.display = 'none');
    const form = document.getElementById('editForm');
    form.action = '/admin/courses/' + course.id;
    form.querySelector('[name="name"]').value          = course.name;
    form.querySelector('[name="code"]').value          = course.code;
    form.querySelector('[name="exam_type"]').value     = course.exam_type;
    form.querySelector('[name="target_year"]').value   = course.target_year;
    form.querySelector('[name="duration_months"]').value = course.duration_months;
    form.querySelector('[name="total_questions"]').value = course.total_questions ?? '';
    document.getElementById('editModal').style.display = 'flex';
}

@if ($errors->any())
    document.getElementById('addModal').style.display = 'flex';
@endif
</script>

@endsection
