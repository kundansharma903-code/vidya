@extends('layouts.admin')
@section('title', 'Students')

@section('content')
@php
$avatarPalette = [
    ['bg' => '#5f7eb4', 'color' => '#f5f1e8'],
    ['bg' => '#7fb685', 'color' => '#f5f1e8'],
    ['bg' => '#a392c8', 'color' => '#f5f1e8'],
    ['bg' => '#d4a574', 'color' => '#f5f1e8'],
    ['bg' => '#6ab0b2', 'color' => '#f5f1e8'],
];
@endphp

<div style="padding:32px 36px;max-width:1280px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;margin:0 0 6px 0;letter-spacing:-0.56px;">Students</h1>
            <div style="display:flex;align-items:center;gap:10px;font-size:13px;">
                <span style="font-weight:500;color:#f5f1e8;">{{ $stats->total ?? 0 }} total</span>
                <span style="color:#6a665f;">·</span>
                <span style="color:#7fb685;">{{ $stats->active_count ?? 0 }} active</span>
                <span style="color:#6a665f;">·</span>
                <span style="color:#a8a39c;">{{ $stats->neet_count ?? 0 }} NEET</span>
                <span style="color:#6a665f;">·</span>
                <span style="color:#a8a39c;">{{ $stats->jee_count ?? 0 }} JEE</span>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <button style="display:flex;align-items:center;gap:8px;background:#14141b;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;font-weight:500;color:#f5f1e8;cursor:pointer;">
                <span style="color:#a8a39c;font-size:14px;">↑</span>
                Import from Excel
            </button>
            <button onclick="document.getElementById('addModal').style.display='flex'"
                    style="display:flex;align-items:center;gap:6px;background:#7a95c8;color:#14141b;border:none;border-radius:6px;padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 6px rgba(122,149,200,0.2);">
                <span style="font-size:14px;font-weight:700;">+</span>
                Add Student
            </button>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div style="background:rgba(127,182,133,0.10);border:1px solid rgba(127,182,133,0.25);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#7fb685;">
        {{ session('success') }}
    </div>
    @endif

    {{-- Filters --}}
    <form method="GET" style="display:flex;gap:12px;margin-bottom:20px;align-items:center;">
        <div style="flex:1;display:flex;align-items:center;gap:10px;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px;"
             onfocusin="this.style.borderColor='rgba(122,149,200,0.4)'"
             onfocusout="this.style.borderColor='rgba(245,241,232,0.10)'">
            <span style="color:#6a665f;font-size:14px;">⌕</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by roll number, name, or phone…"
                   style="flex:1;background:transparent;border:none;outline:none;font-size:13px;color:#f5f1e8;min-width:0;"
                   autocomplete="off">
        </div>

        <select name="batch_id" onchange="this.form.submit()"
                style="background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px;font-size:13px;color:{{ request('batch_id') ? '#f5f1e8' : '#a8a39c' }};outline:none;cursor:pointer;width:160px;">
            <option value="">All Batches</option>
            @foreach($batches as $batch)
                <option value="{{ $batch->id }}" {{ request('batch_id') == $batch->id ? 'selected' : '' }}>{{ $batch->name }}</option>
            @endforeach
        </select>

        <select name="course_id" onchange="this.form.submit()"
                style="background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px;font-size:13px;color:{{ request('course_id') ? '#f5f1e8' : '#a8a39c' }};outline:none;cursor:pointer;width:150px;">
            <option value="">All Courses</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
            @endforeach
        </select>

        <select name="status" onchange="this.form.submit()"
                style="background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:11px 14px;font-size:13px;color:{{ request('status') ? '#f5f1e8' : '#a8a39c' }};outline:none;cursor:pointer;width:140px;">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        @if(request('search') || request('batch_id') || request('course_id') || request('status'))
            <a href="{{ route('admin.students') }}"
               style="padding:11px 14px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:6px;font-size:13px;color:#a8a39c;text-decoration:none;white-space:nowrap;">
                Clear
            </a>
        @else
            <button type="submit"
                    style="padding:11px 18px;background:#7a95c8;color:#08080a;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">
                Search
            </button>
        @endif
    </form>

    {{-- Table --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;overflow:hidden;">
        @if($students->isEmpty())
        <div style="padding:60px 24px;text-align:center;">
            <div style="width:48px;height:48px;background:rgba(245,241,232,0.04);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <circle cx="10" cy="7" r="3" stroke="#6a665f" stroke-width="1.5"/>
                    <path d="M3 18c0-3.866 3.134-7 7-7s7 3.134 7 7" stroke="#6a665f" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </div>
            <p style="font-size:14px;color:#a8a39c;margin:0 0 6px;">No students found</p>
            <p style="font-size:12px;color:#6a665f;margin:0;">Add your first student to get started.</p>
        </div>
        @else
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);">
                    <th style="padding:12px 20px;width:24px;"></th>
                    <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;width:84px;white-space:nowrap;">Roll No</th>
                    <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;">Student</th>
                    <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;width:200px;white-space:nowrap;">Batch · Course</th>
                    <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;width:120px;">Phone</th>
                    <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;width:110px;">Admitted</th>
                    <th style="padding:12px 16px;text-align:left;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;width:90px;">Status</th>
                    <th style="width:32px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                @php
                    $idx      = ord(strtoupper($student->name[0])) % 5;
                    $av       = $avatarPalette[$idx];
                    $initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', trim($student->name)), 0, 2)));
                @endphp
                <tr style="border-bottom:1px solid rgba(245,241,232,0.05);"
                    onmouseover="this.style.background='rgba(245,241,232,0.02)'"
                    onmouseout="this.style.background='transparent'">

                    {{-- Checkbox --}}
                    <td style="padding:0 20px;width:24px;">
                        <div style="width:16px;height:16px;background:#0f0f14;border:1px solid rgba(245,241,232,0.15);border-radius:3px;"></div>
                    </td>

                    {{-- Roll No --}}
                    <td style="padding:0 16px;height:60px;">
                        <span style="font-size:13px;font-weight:500;color:#a8a39c;">{{ $student->roll_number }}</span>
                    </td>

                    {{-- Student --}}
                    <td style="padding:0 16px;height:60px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:{{ $av['bg'] }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:{{ $av['color'] }};flex-shrink:0;">
                                {{ $initials }}
                            </div>
                            <div>
                                <div style="font-size:14px;font-weight:500;color:#f5f1e8;">{{ $student->name }}</div>
                                <div style="font-size:11px;color:#6a665f;margin-top:2px;">
                                    Enroll · {{ $student->enrollment_number ?: 'EN' . $student->roll_number }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Batch · Course --}}
                    <td style="padding:0 16px;height:60px;">
                        <div style="font-size:13px;color:#f5f1e8;">{{ $student->batch_name }}</div>
                        <div style="font-size:11px;color:#6a665f;margin-top:2px;">{{ $student->course_name }}</div>
                    </td>

                    {{-- Phone --}}
                    <td style="padding:0 16px;height:60px;">
                        <span style="font-size:13px;color:#a8a39c;">{{ $student->phone ?: '—' }}</span>
                    </td>

                    {{-- Admitted --}}
                    <td style="padding:0 16px;height:60px;">
                        <span style="font-size:13px;color:#a8a39c;">
                            {{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d M Y') : '—' }}
                        </span>
                    </td>

                    {{-- Status --}}
                    <td style="padding:0 16px;height:60px;">
                        @if($student->is_active)
                        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:500;background:rgba(127,182,133,0.12);color:#7fb685;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#7fb685;flex-shrink:0;"></span> Active
                        </span>
                        @else
                        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:9999px;font-size:11px;font-weight:500;background:rgba(106,102,95,0.12);color:#6a665f;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#6a665f;flex-shrink:0;"></span> Inactive
                        </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td style="padding:0 16px;height:60px;width:32px;">
                        <div style="display:flex;align-items:center;gap:4px;">
                            <button onclick="openEditModal({{ json_encode($student) }})"
                                    style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:6px;cursor:pointer;"
                                    title="Edit"
                                    onmouseover="this.style.background='rgba(122,149,200,0.12)'"
                                    onmouseout="this.style.background='rgba(245,241,232,0.05)'">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M8.5 1.5L10.5 3.5L4 10H2v-2L8.5 1.5z" stroke="#a8a39c" stroke-width="1.2" stroke-linejoin="round"/>
                                </svg>
                            </button>

                            <form method="POST" action="{{ route('admin.students.toggle', $student->id) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:6px;cursor:pointer;"
                                        title="{{ $student->is_active ? 'Deactivate' : 'Activate' }}"
                                        onmouseover="this.style.background='rgba(212,165,116,0.12)'"
                                        onmouseout="this.style.background='rgba(245,241,232,0.05)'">
                                    @if($student->is_active)
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <circle cx="6" cy="6" r="4.5" stroke="#d4a574" stroke-width="1.2"/>
                                        <path d="M4 6h4" stroke="#d4a574" stroke-width="1.2" stroke-linecap="round"/>
                                    </svg>
                                    @else
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <circle cx="6" cy="6" r="4.5" stroke="#7fb685" stroke-width="1.2"/>
                                        <path d="M4 6h4M6 4v4" stroke="#7fb685" stroke-width="1.2" stroke-linecap="round"/>
                                    </svg>
                                    @endif
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.students.destroy', $student->id) }}" style="display:inline;"
                                  onsubmit="return confirm('Remove {{ addslashes($student->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:6px;cursor:pointer;"
                                        title="Remove"
                                        onmouseover="this.style.background='rgba(200,112,100,0.12)'"
                                        onmouseout="this.style.background='rgba(245,241,232,0.05)'">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                        <path d="M2 3h8M5 3V2h2v1M4 3v6h4V3" stroke="#c87064" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($students->lastPage() > 1)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid rgba(245,241,232,0.06);">
            <span style="font-size:13px;color:#6a665f;">
                Showing {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }} students
            </span>
            <div style="display:flex;align-items:center;gap:4px;">
                {{-- Prev --}}
                @if($students->onFirstPage())
                <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;font-size:12px;color:#6a665f;">‹</span>
                @else
                <a href="{{ $students->previousPageUrl() }}"
                   style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;font-size:12px;color:#a8a39c;text-decoration:none;background:rgba(245,241,232,0.05);">‹</a>
                @endif

                {{-- Pages with ellipsis --}}
                @php
                    $current  = $students->currentPage();
                    $last     = $students->lastPage();
                    $window   = collect(range(max(1, $current-1), min($last, $current+1)));
                    $showHead = $window->first() > 2;
                    $showTail = $window->last() < $last - 1;
                @endphp

                @if($window->first() > 1)
                <a href="{{ $students->url(1) }}"
                   style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;font-size:12px;text-decoration:none;background:rgba(245,241,232,0.05);color:#a8a39c;">1</a>
                @endif
                @if($showHead)
                <span style="font-size:12px;color:#6a665f;padding:0 2px;">…</span>
                @endif

                @foreach($window as $page)
                <a href="{{ $students->url($page) }}"
                   style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;font-size:12px;text-decoration:none;
                          {{ $page == $current ? 'background:#7a95c8;color:#14141b;font-weight:600;' : 'background:rgba(245,241,232,0.05);color:#a8a39c;border:1px solid rgba(245,241,232,0.10);' }}">
                    {{ $page }}
                </a>
                @endforeach

                @if($showTail)
                <span style="font-size:12px;color:#6a665f;padding:0 2px;">…</span>
                @endif
                @if($window->last() < $last)
                <a href="{{ $students->url($last) }}"
                   style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;font-size:12px;text-decoration:none;background:rgba(245,241,232,0.05);color:#a8a39c;border:1px solid rgba(245,241,232,0.10);">{{ $last }}</a>
                @endif

                {{-- Next --}}
                @if($students->hasMorePages())
                <a href="{{ $students->nextPageUrl() }}"
                   style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;font-size:12px;color:#a8a39c;text-decoration:none;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.10);">›</a>
                @else
                <span style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;font-size:12px;color:#6a665f;">›</span>
                @endif
            </div>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- Add Modal --}}
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.8);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:16px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.06);">
            <h3 style="font-size:15px;font-weight:600;color:#f5f1e8;margin:0;">Add Student</h3>
            <button onclick="document.getElementById('addModal').style.display='none'"
                    style="width:28px;height:28px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6a665f;font-size:16px;">×</button>
        </div>
        <form method="POST" action="{{ route('admin.students.store') }}">
            @csrf
            <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
                @include('admin.students._form')
            </div>
            <div style="padding:16px 24px;border-top:1px solid rgba(245,241,232,0.06);display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                        style="padding:9px 18px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:8px;font-size:13px;color:#a8a39c;cursor:pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:9px 18px;background:#7a95c8;color:#08080a;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                    Add Student
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.8);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:16px;width:100%;max-width:600px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.06);">
            <h3 style="font-size:15px;font-weight:600;color:#f5f1e8;margin:0;">Edit Student</h3>
            <button onclick="document.getElementById('editModal').style.display='none'"
                    style="width:28px;height:28px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6a665f;font-size:16px;">×</button>
        </div>
        <form method="POST" id="editForm" action="">
            @csrf @method('PUT')
            <div style="padding:24px;display:flex;flex-direction:column;gap:16px;" id="editFields">
            </div>
            <div style="padding:16px 24px;border-top:1px solid rgba(245,241,232,0.06);display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" onclick="document.getElementById('editModal').style.display='none'"
                        style="padding:9px 18px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:8px;font-size:13px;color:#a8a39c;cursor:pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:9px 18px;background:#7a95c8;color:#08080a;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
var batchOptions = [
    @foreach($batches as $b)
    { id: {{ $b->id }}, name: "{{ addslashes($b->name) }}" },
    @endforeach
];

var IS = "width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;";
var LS = "display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;";

function openEditModal(s) {
    document.getElementById('editForm').action = '/admin/students/' + s.id;

    var batchSel = batchOptions.map(function(b) {
        return '<option value="' + b.id + '"' + (b.id == s.batch_id ? ' selected' : '') + '>' + b.name + '</option>';
    }).join('');

    document.getElementById('editFields').innerHTML =
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">' +
            '<div><label style="' + LS + '">Full Name</label>' +
            '<input type="text" name="name" value="' + (s.name||'') + '" required style="' + IS + '"></div>' +
            '<div><label style="' + LS + '">Batch</label>' +
            '<select name="batch_id" required style="' + IS + 'cursor:pointer;"><option value="">Select batch…</option>' + batchSel + '</select></div>' +
        '</div>' +
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">' +
            '<div><label style="' + LS + '">Roll Number</label>' +
            '<input type="text" name="roll_number" value="' + (s.roll_number||'') + '" required style="' + IS + '"></div>' +
            '<div><label style="' + LS + '">Enrollment No</label>' +
            '<input type="text" name="enrollment_number" value="' + (s.enrollment_number||'') + '" style="' + IS + '"></div>' +
        '</div>' +
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">' +
            '<div><label style="' + LS + '">Phone</label>' +
            '<input type="text" name="phone" value="' + (s.phone||'') + '" style="' + IS + '"></div>' +
            '<div><label style="' + LS + '">Parent Phone</label>' +
            '<input type="text" name="parent_phone" value="' + (s.parent_phone||'') + '" style="' + IS + '"></div>' +
        '</div>' +
        '<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">' +
            '<div><label style="' + LS + '">Email</label>' +
            '<input type="email" name="email" value="' + (s.email||'') + '" style="' + IS + '"></div>' +
            '<div><label style="' + LS + '">Admission Date</label>' +
            '<input type="date" name="admission_date" value="' + (s.admission_date||'') + '" style="' + IS + 'color-scheme:dark;"></div>' +
        '</div>';

    document.getElementById('editModal').style.display = 'flex';
}

['addModal','editModal'].forEach(function(id) {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});

@if($errors->any())
document.getElementById('addModal').style.display = 'flex';
@endif
</script>
@endsection
