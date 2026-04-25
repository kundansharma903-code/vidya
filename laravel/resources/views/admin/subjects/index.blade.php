@extends('layouts.admin')

@section('title', 'Subjects')
@section('breadcrumb', 'Subjects')

@section('content')

@php
// 5-color palette cycling by first letter ASCII
$avatarPalette = [
    ['bg' => 'rgba(122,149,200,0.15)', 'border' => 'rgba(122,149,200,0.3)',  'text' => '#7a95c8'],
    ['bg' => 'rgba(127,182,133,0.15)', 'border' => 'rgba(127,182,133,0.3)',  'text' => '#7fb685'],
    ['bg' => 'rgba(200,112,100,0.15)', 'border' => 'rgba(200,112,100,0.3)',  'text' => '#c87064'],
    ['bg' => 'rgba(163,146,200,0.15)', 'border' => 'rgba(163,146,200,0.3)',  'text' => '#a392c8'],
    ['bg' => 'rgba(212,165,116,0.15)', 'border' => 'rgba(212,165,116,0.3)',  'text' => '#d4a574'],
];

$examBadge = [
    'BOTH' => ['label' => 'NEET + JEE', 'color' => '#7a95c8', 'bg' => 'rgba(122,149,200,0.12)', 'dot' => '#7a95c8'],
    'NEET' => ['label' => 'NEET only',  'color' => '#7fb685', 'bg' => 'rgba(127,182,133,0.12)', 'dot' => '#7fb685'],
    'JEE'  => ['label' => 'JEE only',   'color' => '#d4a574', 'bg' => 'rgba(212,165,116,0.12)', 'dot' => '#d4a574'],
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
        <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px 0;">Subjects</h1>
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;">
            <span style="font-weight:500;color:#f5f1e8;">{{ $stats->total ?? 0 }} total</span>
            <span style="color:#6a665f;">·</span>
            <span style="color:#7fb685;">{{ $stats->active_count ?? 0 }} active</span>
            @if ($totalSubtopics > 0)
                <span style="color:#6a665f;">·</span>
                <span style="color:#a8a39c;">{{ number_format($totalSubtopics) }} subtopics pre-loaded</span>
            @endif
        </div>
    </div>
    <button onclick="document.getElementById('addModal').style.display='flex'"
            style="display:flex;align-items:center;gap:6px;background:#7a95c8;color:#14141b;border:none;border-radius:6px;padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 6px rgba(122,149,200,0.2);">
        <span style="font-size:16px;font-weight:700;line-height:1;">+</span>
        Add Subject
    </button>
</div>

{{-- Filter Bar --}}
<form method="GET" action="{{ route('admin.subjects') }}" style="display:flex;gap:12px;margin-bottom:20px;">
    <div style="flex:1;position:relative;">
        <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#6a665f;font-size:14px;pointer-events:none;">⌕</span>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search subject name or code…"
               style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px 11px 36px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    </div>
    <select name="exam_type"
            style="background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px;font-size:13px;color:#a8a39c;width:170px;outline:none;cursor:pointer;"
            onchange="this.form.submit()">
        <option value="">All Exam Types</option>
        <option value="BOTH" {{ request('exam_type') === 'BOTH' ? 'selected' : '' }}>NEET + JEE</option>
        <option value="NEET" {{ request('exam_type') === 'NEET' ? 'selected' : '' }}>NEET only</option>
        <option value="JEE"  {{ request('exam_type') === 'JEE'  ? 'selected' : '' }}>JEE only</option>
    </select>
</form>

{{-- Table --}}
<div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;overflow:hidden;margin-bottom:20px;">

    {{-- Header --}}
    <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:flex;align-items:center;gap:12px;height:44px;padding:0 20px;">
        <div style="width:24px;flex-shrink:0;"></div>
        <div style="flex:1;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Subject</div>
        <div style="width:130px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Exam Type</div>
        <div style="width:120px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Chapters</div>
        <div style="width:110px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Topics</div>
        <div style="width:120px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Subtopics</div>
        <div style="width:110px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Order</div>
        <div style="width:90px;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Status</div>
        <div style="width:32px;flex-shrink:0;"></div>
    </div>

    {{-- Rows --}}
    @forelse ($subjects as $subject)
    @php
        $palette   = $avatarPalette[ord(strtoupper($subject->name[0])) % 5];
        $badge     = $examBadge[$subject->exam_type] ?? $examBadge['BOTH'];
        $initial   = strtoupper($subject->name[0]);
        $subtopics = $subject->subtopic_count ?? 0;
        $statusColor = $subject->is_active ? '#7fb685' : '#a8a39c';
        $statusBg    = $subject->is_active ? 'rgba(127,182,133,0.12)' : 'rgba(168,163,156,0.12)';
        $statusLabel = $subject->is_active ? 'Active' : 'Inactive';
    @endphp
    <div style="display:flex;align-items:center;gap:12px;height:60px;padding:0 20px;border-bottom:1px solid rgba(245,241,232,0.05);"
         onmouseover="this.style.background='rgba(26,26,36,0.4)'"
         onmouseout="this.style.background=''">

        {{-- Checkbox --}}
        <div style="width:24px;flex-shrink:0;display:flex;align-items:center;">
            <input type="checkbox" style="width:16px;height:16px;accent-color:#7a95c8;cursor:pointer;">
        </div>

        {{-- Subject name + letter avatar --}}
        <div style="flex:1;min-width:0;display:flex;align-items:center;gap:14px;">
            <div style="width:40px;height:40px;background:{{ $palette['bg'] }};border:1px solid {{ $palette['border'] }};border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="font-size:18px;font-weight:700;color:{{ $palette['text'] }};">{{ $initial }}</span>
            </div>
            <div>
                <div style="font-size:14px;font-weight:600;color:#f5f1e8;">{{ $subject->name }}</div>
                <div style="font-size:11px;color:#6a665f;margin-top:2px;">
                    {{ number_format($subtopics) }} subtopics · click to view tree
                </div>
            </div>
        </div>

        {{-- Exam Type --}}
        <div style="width:130px;flex-shrink:0;">
            <span style="display:inline-flex;align-items:center;gap:6px;background:{{ $badge['bg'] }};border-radius:9999px;padding:4px 10px 4px 8px;">
                <span style="width:6px;height:6px;background:{{ $badge['dot'] }};border-radius:50%;flex-shrink:0;"></span>
                <span style="font-size:11px;font-weight:500;color:{{ $badge['color'] }};white-space:nowrap;">{{ $badge['label'] }}</span>
            </span>
        </div>

        {{-- Chapters --}}
        <div style="width:120px;flex-shrink:0;">
            <span style="font-size:14px;font-weight:500;color:#f5f1e8;">{{ $subject->chapter_count ?? 0 }}</span>
        </div>

        {{-- Topics --}}
        <div style="width:110px;flex-shrink:0;">
            <span style="font-size:14px;font-weight:500;color:#f5f1e8;">{{ $subject->topic_count ?? 0 }}</span>
        </div>

        {{-- Subtopics --}}
        <div style="width:120px;flex-shrink:0;">
            <span style="font-size:14px;font-weight:500;color:#f5f1e8;">{{ $subtopics }}</span>
        </div>

        {{-- Order --}}
        <div style="width:110px;flex-shrink:0;display:flex;align-items:center;gap:8px;">
            <span style="color:#6a665f;font-size:14px;cursor:grab;" title="Drag to reorder">⋮⋮</span>
            <span style="font-size:13px;font-weight:500;color:#a8a39c;">#{{ $subject->display_order }}</span>
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
            <button onclick="toggleMenu({{ $subject->id }})"
                    style="width:28px;height:28px;background:none;border:none;border-radius:4px;cursor:pointer;color:#6a665f;font-size:14px;font-weight:700;display:flex;align-items:center;justify-content:center;"
                    onmouseover="this.style.background='rgba(245,241,232,0.06)'"
                    onmouseout="this.style.background='none'">•••</button>
            <div id="menu-{{ $subject->id }}" style="display:none;position:absolute;right:0;top:32px;background:#1a1a24;border:1px solid rgba(245,241,232,0.1);border-radius:6px;min-width:140px;z-index:100;box-shadow:0 8px 24px rgba(0,0,0,0.4);">
                <button onclick="openEditModal({{ json_encode($subject) }})"
                        style="width:100%;text-align:left;background:none;border:none;padding:10px 14px;font-size:13px;color:#a8a39c;cursor:pointer;"
                        onmouseover="this.style.color='#f5f1e8';this.style.background='rgba(245,241,232,0.04)'"
                        onmouseout="this.style.color='#a8a39c';this.style.background='none'">Edit</button>
                <form method="POST" action="{{ route('admin.subjects.toggle', $subject->id) }}" style="margin:0;">
                    @csrf @method('PATCH')
                    <button type="submit"
                            style="width:100%;text-align:left;background:none;border:none;padding:10px 14px;font-size:13px;color:#a8a39c;cursor:pointer;"
                            onmouseover="this.style.color='#f5f1e8';this.style.background='rgba(245,241,232,0.04)'"
                            onmouseout="this.style.color='#a8a39c';this.style.background='none'">
                        {{ $subject->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.subjects.destroy', $subject->id) }}" style="margin:0;"
                      onsubmit="return confirm('Delete this subject and all its curriculum? This cannot be undone.')">
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
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <p style="font-size:14px;color:#6a665f;margin:0 0 4px 0;">No subjects yet</p>
        <p style="font-size:12px;color:#4a4640;margin:0;">Click "Add Subject" to add Physics, Chemistry, etc.</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div style="display:flex;align-items:center;justify-content:space-between;">
    <span style="font-size:13px;color:#6a665f;">
        Showing {{ $subjects->firstItem() ?? 0 }}–{{ $subjects->lastItem() ?? 0 }} of {{ $subjects->total() }} subject{{ $subjects->total() !== 1 ? 's' : '' }}
    </span>
    @if ($subjects->hasPages())
    <div style="display:flex;gap:4px;align-items:center;">
        @if ($subjects->onFirstPage())
            <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#4a4640;border-radius:6px;">‹</span>
        @else
            <a href="{{ $subjects->previousPageUrl() }}" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#6a665f;border-radius:6px;text-decoration:none;border:1px solid rgba(245,241,232,0.08);">‹</a>
        @endif
        @foreach ($subjects->getUrlRange(1, $subjects->lastPage()) as $page => $url)
            @if ($page === $subjects->currentPage())
                <span style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#14141b;background:#7a95c8;border-radius:6px;">{{ $page }}</span>
            @else
                <a href="{{ $url }}" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#6a665f;border-radius:6px;text-decoration:none;border:1px solid rgba(245,241,232,0.08);">{{ $page }}</a>
            @endif
        @endforeach
        @if ($subjects->hasMorePages())
            <a href="{{ $subjects->nextPageUrl() }}" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;font-size:12px;color:#6a665f;border-radius:6px;text-decoration:none;border:1px solid rgba(245,241,232,0.08);">›</a>
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
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:10px;width:440px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.06);">
            <span style="font-size:16px;font-weight:600;color:#f5f1e8;">Add Subject</span>
            <button onclick="document.getElementById('addModal').style.display='none'"
                    style="background:none;border:none;cursor:pointer;color:#6a665f;font-size:20px;line-height:1;padding:0;">×</button>
        </div>
        <form method="POST" action="{{ route('admin.subjects.store') }}" style="padding:24px;display:flex;flex-direction:column;gap:18px;">
            @csrf
            @include('admin.subjects._form')
            <div style="display:flex;gap:10px;justify-content:flex-end;padding-top:8px;border-top:1px solid rgba(245,241,232,0.06);">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                        style="background:none;border:1px solid rgba(245,241,232,0.12);border-radius:6px;padding:9px 18px;font-size:13px;color:#a8a39c;cursor:pointer;">Cancel</button>
                <button type="submit"
                        style="background:#7a95c8;border:none;border-radius:6px;padding:9px 18px;font-size:13px;font-weight:600;color:#14141b;cursor:pointer;">Create Subject</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.75);z-index:200;align-items:center;justify-content:center;" onclick="if(event.target===this)this.style.display='none'">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:10px;width:440px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.06);">
            <span style="font-size:16px;font-weight:600;color:#f5f1e8;">Edit Subject</span>
            <button onclick="document.getElementById('editModal').style.display='none'"
                    style="background:none;border:none;cursor:pointer;color:#6a665f;font-size:20px;line-height:1;padding:0;">×</button>
        </div>
        <form id="editForm" method="POST" style="padding:24px;display:flex;flex-direction:column;gap:18px;">
            @csrf @method('PUT')
            @include('admin.subjects._form', ['edit' => true])
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
function openEditModal(subject) {
    document.querySelectorAll('[id^="menu-"]').forEach(m => m.style.display = 'none');
    const form = document.getElementById('editForm');
    form.action = '/admin/subjects/' + subject.id;
    form.querySelector('[name="name"]').value          = subject.name;
    form.querySelector('[name="code"]').value          = subject.code;
    form.querySelector('[name="exam_type"]').value     = subject.exam_type;
    form.querySelector('[name="display_order"]').value = subject.display_order;
    document.getElementById('editModal').style.display = 'flex';
}
@if ($errors->any())
    document.getElementById('addModal').style.display = 'flex';
@endif
</script>

@endsection
