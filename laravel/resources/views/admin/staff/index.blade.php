@extends('layouts.admin')
@section('title', 'Staff Management')

@section('content')
@php
$avatarPalette = [
    ['bg' => 'rgba(122,149,200,0.15)', 'color' => '#7a95c8'],
    ['bg' => 'rgba(127,182,133,0.15)', 'color' => '#7fb685'],
    ['bg' => 'rgba(163,146,200,0.15)', 'color' => '#a392c8'],
    ['bg' => 'rgba(212,165,116,0.15)', 'color' => '#d4a574'],
    ['bg' => 'rgba(106,176,178,0.15)', 'color' => '#6ab0b2'],
];

$roleMeta = [
    'owner'         => ['label' => 'Owner',         'bg' => 'rgba(163,146,200,0.12)', 'color' => '#a392c8'],
    'academic_head' => ['label' => 'Academic Head',  'bg' => 'rgba(122,149,200,0.12)', 'color' => '#7a95c8'],
    'admin'         => ['label' => 'Admin',          'bg' => 'rgba(106,176,178,0.12)', 'color' => '#6ab0b2'],
    'sub_admin'     => ['label' => 'Sub-Admin',      'bg' => 'rgba(200,154,106,0.12)', 'color' => '#c89a6a'],
    'teacher'       => ['label' => 'Teacher',        'bg' => 'rgba(127,182,133,0.12)', 'color' => '#7fb685'],
    'reception'     => ['label' => 'Reception',      'bg' => 'rgba(200,112,100,0.12)', 'color' => '#c87064'],
];

$subjectCodeColors = [
    'P' => ['bg'=>'rgba(95,126,180,0.15)',  'color'=>'#5f7eb4'],
    'C' => ['bg'=>'rgba(127,182,133,0.15)', 'color'=>'#7fb685'],
    'B' => ['bg'=>'rgba(163,146,200,0.15)', 'color'=>'#a392c8'],
    'Z' => ['bg'=>'rgba(200,112,100,0.15)', 'color'=>'#c87064'],
    'M' => ['bg'=>'rgba(212,165,116,0.15)', 'color'=>'#d4a574'],
    'E' => ['bg'=>'rgba(106,176,178,0.15)', 'color'=>'#6ab0b2'],
];

function assignedToLabel($member, $subjectNames) {
    switch ($member->role) {
        case 'owner':         return 'Full institute access';
        case 'academic_head': return 'All reports · Cross-batch';
        case 'admin':         return 'System setup · All users';
        case 'sub_admin':     return 'Limited admin access';
        case 'reception':     return 'Walk-in lookups';
        case 'teacher':
            $subj    = $subjectNames[$member->id] ?? null;
            $batches = $member->batch_count ?? 0;
            if ($subj && $batches) return $subj . ' · ' . $batches . ' ' . ($batches == 1 ? 'batch' : 'batches');
            if ($subj)    return $subj;
            if ($batches) return $batches . ' ' . ($batches == 1 ? 'batch' : 'batches');
            return 'No assignments yet';
        default: return '—';
    }
}
@endphp

<div style="padding:32px 36px;max-width:1280px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:28px;">
        <div>
            <h1 style="font-size:22px;font-weight:600;color:#f5f1e8;margin:0 0 4px 0;">Staff Management</h1>
            <p style="font-size:13px;color:#6a665f;margin:0;">
                {{ $stats->total ?? 0 }} total
                · {{ $stats->active_count ?? 0 }} active
                · {{ $stats->teacher_count ?? 0 }} teachers
                · {{ $stats->reception_count ?? 0 }} reception
            </p>
        </div>
        <button onclick="document.getElementById('addModal').style.display='flex'"
                style="display:flex;align-items:center;gap:8px;background:#7a95c8;color:#08080a;border:none;border-radius:8px;padding:10px 18px;font-size:13px;font-weight:600;cursor:pointer;">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M7 1v12M1 7h12" stroke="#08080a" stroke-width="2" stroke-linecap="round"/>
            </svg>
            Create Staff
        </button>
    </div>

    {{-- Flash --}}
    @if(session('success'))
    <div style="background:rgba(127,182,133,0.10);border:1px solid rgba(127,182,133,0.25);border-radius:8px;padding:12px 16px;margin-bottom:20px;font-size:13px;color:#7fb685;">
        {{ session('success') }}
    </div>
    @endif

    {{-- Filters --}}
    <form method="GET" style="display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email…"
               style="flex:1;min-width:200px;background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:9px 14px;font-size:13px;color:#f5f1e8;outline:none;"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.08)'">
        <select name="role" onchange="this.form.submit()"
                style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:9px 14px;font-size:13px;color:{{ request('role') ? '#f5f1e8' : '#6a665f' }};outline:none;cursor:pointer;">
            <option value="">All Roles</option>
            <option value="owner"         {{ request('role') === 'owner'         ? 'selected' : '' }}>Owner</option>
            <option value="academic_head" {{ request('role') === 'academic_head' ? 'selected' : '' }}>Academic Head</option>
            <option value="admin"         {{ request('role') === 'admin'         ? 'selected' : '' }}>Admin</option>
            <option value="sub_admin"     {{ request('role') === 'sub_admin'     ? 'selected' : '' }}>Sub-Admin</option>
            <option value="teacher"       {{ request('role') === 'teacher'       ? 'selected' : '' }}>Teacher</option>
            <option value="reception"     {{ request('role') === 'reception'     ? 'selected' : '' }}>Reception</option>
        </select>
        <select name="status" onchange="this.form.submit()"
                style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:9px 14px;font-size:13px;color:{{ request('status') ? '#f5f1e8' : '#6a665f' }};outline:none;cursor:pointer;">
            <option value="">All Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        @if(request('search') || request('role') || request('status'))
            <a href="{{ route('admin.staff') }}"
               style="display:flex;align-items:center;padding:9px 14px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:8px;font-size:13px;color:#a8a39c;text-decoration:none;">
                Clear
            </a>
        @endif
        <button type="submit"
                style="padding:9px 18px;background:#7a95c8;color:#08080a;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">
            Search
        </button>
    </form>

    {{-- Table --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:12px;overflow:hidden;">
        @if($staff->isEmpty())
        <div style="padding:60px 24px;text-align:center;">
            <p style="font-size:14px;color:#a8a39c;margin:0 0 6px;">No staff found</p>
            <p style="font-size:12px;color:#6a665f;margin:0;">Create your first staff member to get started.</p>
        </div>
        @else
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid rgba(245,241,232,0.06);">
                    <th style="padding:12px 20px;text-align:left;font-size:11px;font-weight:500;color:#6a665f;letter-spacing:0.44px;text-transform:uppercase;white-space:nowrap;">Staff</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:500;color:#6a665f;letter-spacing:0.44px;text-transform:uppercase;">Role</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:500;color:#6a665f;letter-spacing:0.44px;text-transform:uppercase;">Subject</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:500;color:#6a665f;letter-spacing:0.44px;text-transform:uppercase;white-space:nowrap;">Assigned To</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:500;color:#6a665f;letter-spacing:0.44px;text-transform:uppercase;white-space:nowrap;">Last Active</th>
                    <th style="padding:12px 16px;text-align:left;font-size:11px;font-weight:500;color:#6a665f;letter-spacing:0.44px;text-transform:uppercase;">Status</th>
                    <th style="padding:12px 16px;text-align:right;font-size:11px;font-weight:500;color:#6a665f;letter-spacing:0.44px;text-transform:uppercase;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff as $member)
                @php
                    $idx      = ord(strtoupper($member->name[0])) % 5;
                    $av       = $avatarPalette[$idx];
                    $rm       = $roleMeta[$member->role] ?? ['label' => $member->role, 'bg' => 'rgba(245,241,232,0.06)', 'color' => '#a8a39c'];
                    $initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', trim($member->name)), 0, 2)));
                    $assignedLabel = assignedToLabel($member, $subjectNames);
                    $subjCode = strtoupper($member->primary_subject_code ?? '');
                    $sc       = $subjectCodeColors[$subjCode] ?? null;
                    $lastActive = null;
                    $lastActiveDot = '#6a665f';
                    if ($member->last_login_at) {
                        $dt = \Carbon\Carbon::parse($member->last_login_at);
                        if ($dt->diffInMinutes(now()) < 10) {
                            $lastActive    = 'Active now';
                            $lastActiveDot = '#7fb685';
                        } else {
                            $lastActive = $dt->diffForHumans();
                        }
                    }
                @endphp
                <tr style="border-bottom:1px solid rgba(245,241,232,0.04);"
                    onmouseover="this.style.background='rgba(245,241,232,0.02)'"
                    onmouseout="this.style.background='transparent'">

                    {{-- Staff --}}
                    <td style="padding:14px 20px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:{{ $av['bg'] }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:{{ $av['color'] }};flex-shrink:0;letter-spacing:0.5px;">
                                {{ $initials }}
                            </div>
                            <div>
                                <div style="font-size:13px;font-weight:500;color:#f5f1e8;">{{ $member->name }}</div>
                                <div style="font-size:11px;color:#6a665f;margin-top:1px;">{{ $member->email }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Role --}}
                    <td style="padding:14px 16px;">
                        <span style="display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:{{ $rm['bg'] }};color:{{ $rm['color'] }};white-space:nowrap;">
                            {{ $rm['label'] }}
                        </span>
                    </td>

                    {{-- Subject --}}
                    <td style="padding:14px 16px;">
                        @if($member->role === 'teacher' && $member->primary_subject_name)
                        @php $sc = $subjectCodeColors[strtoupper($member->primary_subject_code ?? '')] ?? ['bg'=>'rgba(245,241,232,0.06)','color'=>'#a8a39c']; @endphp
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:24px;height:24px;border-radius:5px;background:{{ $sc['bg'] }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:{{ $sc['color'] }};flex-shrink:0;">
                                {{ strtoupper($member->primary_subject_code ?? '?') }}
                            </div>
                            <span style="font-size:12px;color:#a8a39c;">{{ $member->primary_subject_name }}</span>
                        </div>
                        @elseif($member->role === 'teacher')
                        <span style="font-size:11px;color:#c87064;background:rgba(200,112,100,0.10);padding:2px 8px;border-radius:4px;">Not set</span>
                        @else
                        <span style="font-size:12px;color:#6a665f;">—</span>
                        @endif
                    </td>

                    {{-- Assigned To --}}
                    <td style="padding:14px 16px;">
                        <span style="font-size:12px;color:#a8a39c;">{{ $assignedLabel }}</span>
                    </td>

                    {{-- Last Active --}}
                    <td style="padding:14px 16px;">
                        @if($lastActive)
                        <div style="display:flex;align-items:center;gap:6px;">
                            <div style="width:6px;height:6px;border-radius:50%;background:{{ $lastActiveDot }};flex-shrink:0;"></div>
                            <span style="font-size:12px;color:{{ $lastActiveDot === '#7fb685' ? '#7fb685' : '#a8a39c' }};">{{ $lastActive }}</span>
                        </div>
                        @else
                        <span style="font-size:12px;color:#6a665f;">Never</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td style="padding:14px 16px;">
                        @if($member->is_active)
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:rgba(127,182,133,0.10);color:#7fb685;">
                            <span style="width:5px;height:5px;border-radius:50%;background:#7fb685;"></span> Active
                        </span>
                        @else
                        <span style="display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:500;background:rgba(245,241,232,0.05);color:#6a665f;">
                            <span style="width:5px;height:5px;border-radius:50%;background:#6a665f;"></span> Inactive
                        </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td style="padding:14px 16px;text-align:right;">
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:6px;">
                            <button onclick="openEditModal({{ json_encode($member) }})"
                                    style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:6px;cursor:pointer;"
                                    title="Edit"
                                    onmouseover="this.style.background='rgba(122,149,200,0.12)'"
                                    onmouseout="this.style.background='rgba(245,241,232,0.05)'">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M8.5 1.5L10.5 3.5L4 10H2v-2L8.5 1.5z" stroke="#a8a39c" stroke-width="1.2" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <form method="POST" action="{{ route('admin.staff.toggle', $member->id) }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:6px;cursor:pointer;"
                                        title="{{ $member->is_active ? 'Deactivate' : 'Activate' }}"
                                        onmouseover="this.style.background='rgba(212,165,116,0.12)'"
                                        onmouseout="this.style.background='rgba(245,241,232,0.05)'">
                                    @if($member->is_active)
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="4.5" stroke="#d4a574" stroke-width="1.2"/><path d="M4 6h4" stroke="#d4a574" stroke-width="1.2" stroke-linecap="round"/></svg>
                                    @else
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><circle cx="6" cy="6" r="4.5" stroke="#7fb685" stroke-width="1.2"/><path d="M4 6h4M6 4v4" stroke="#7fb685" stroke-width="1.2" stroke-linecap="round"/></svg>
                                    @endif
                                </button>
                            </form>
                            @if($member->id !== Auth::id())
                            <form method="POST" action="{{ route('admin.staff.destroy', $member->id) }}" style="display:inline;"
                                  onsubmit="return confirm('Remove {{ addslashes($member->name) }} from staff? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:6px;cursor:pointer;"
                                        title="Remove"
                                        onmouseover="this.style.background='rgba(200,112,100,0.12)'"
                                        onmouseout="this.style.background='rgba(245,241,232,0.05)'">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none"><path d="M2 3h8M5 3V2h2v1M4 3v6h4V3" stroke="#c87064" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        @if($staff->lastPage() > 1)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-top:1px solid rgba(245,241,232,0.06);">
            <span style="font-size:12px;color:#6a665f;">
                Showing {{ $staff->firstItem() }}–{{ $staff->lastItem() }} of {{ $staff->total() }}
            </span>
            <div style="display:flex;gap:4px;">
                @foreach($staff->getUrlRange(1, $staff->lastPage()) as $page => $url)
                <a href="{{ $url }}"
                   style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;font-size:12px;text-decoration:none;
                          {{ $page == $staff->currentPage() ? 'background:#7a95c8;color:#08080a;font-weight:600;' : 'background:rgba(245,241,232,0.05);color:#a8a39c;' }}">
                    {{ $page }}
                </a>
                @endforeach
            </div>
        </div>
        @endif
        @endif
    </div>
</div>

{{-- Add Modal --}}
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.8);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.15);border-radius:12px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.08);">
            <div>
                <h3 style="font-size:18px;font-weight:700;color:#f5f1e8;margin:0 0 4px;letter-spacing:-0.36px;">Add New Staff</h3>
                <p style="font-size:12px;color:#a8a39c;margin:0;">Add teacher, admin, or any staff member</p>
            </div>
            <button onclick="document.getElementById('addModal').style.display='none'"
                    style="width:32px;height:32px;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#a8a39c;font-size:14px;flex-shrink:0;">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.staff.store') }}">
            @csrf
            <div style="padding:22px 24px;display:flex;flex-direction:column;gap:14px;">
                @include('admin.staff._form')
            </div>
            <div style="padding:16px 24px;border-top:1px solid rgba(245,241,232,0.08);display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:11px;color:#6a665f;">* Required fields</span>
                <div style="display:flex;gap:10px;">
                    <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                            style="padding:10px 16px;background:#14141b;border:1px solid rgba(245,241,232,0.15);border-radius:6px;font-size:13px;color:#a8a39c;cursor:pointer;">
                        Cancel
                    </button>
                    <button type="submit"
                            style="display:flex;align-items:center;gap:6px;padding:10px 16px;background:#7a95c8;color:#14141b;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">
                        <span style="font-weight:700;">✓</span> Add Staff
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.8);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.15);border-radius:12px;width:100%;max-width:560px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.08);">
            <div>
                <h3 style="font-size:18px;font-weight:700;color:#f5f1e8;margin:0 0 4px;letter-spacing:-0.36px;">Edit Staff Member</h3>
                <p style="font-size:12px;color:#a8a39c;margin:0;">Update details and subject assignment</p>
            </div>
            <button onclick="document.getElementById('editModal').style.display='none'"
                    style="width:32px;height:32px;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#a8a39c;font-size:14px;flex-shrink:0;">✕</button>
        </div>
        <form method="POST" id="editForm" action="">
            @csrf @method('PUT')
            @php $IS = "width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;"; $LS = "display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;"; @endphp
            <div style="padding:22px 24px;display:flex;flex-direction:column;gap:14px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div>
                        <label style="{{ $LS }}">Full Name</label>
                        <input type="text" name="name" id="edit_name" required style="{{ $IS }}"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    </div>
                    <div>
                        <label style="{{ $LS }}">Phone <span style="color:#6a665f;font-weight:400;text-transform:none;">(optional)</span></label>
                        <input type="text" name="phone" id="edit_phone" style="{{ $IS }}"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    </div>
                </div>
                <div>
                    <label style="{{ $LS }}">Email</label>
                    <input type="email" name="email" id="edit_email" required style="{{ $IS }}"
                           onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div>
                        <label style="{{ $LS }}">Username</label>
                        <input type="text" name="username" id="edit_username" required style="{{ $IS }}"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    </div>
                    <div>
                        <label style="{{ $LS }}">Role</label>
                        <select name="role" id="edit_role" required class="role-select" style="{{ $IS }}cursor:pointer;"
                                onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'"
                                onchange="handleRoleChange(this)">
                            <option value="owner">Owner</option>
                            <option value="academic_head">Academic Head</option>
                            <option value="admin">Admin</option>
                            <option value="sub_admin">Sub-Admin</option>
                            <option value="teacher">Teacher</option>
                            <option value="reception">Reception</option>
                        </select>
                    </div>
                </div>

                {{-- Subject row (edit) --}}
                <div class="subject-row" id="editSubjectRow" style="display:none;">
                    <label style="display:block;font-size:11px;font-weight:500;color:#7a95c8;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;">
                        Subject (Required for Teachers) *
                    </label>
                    <select name="primary_subject_id" id="edit_subject" class="subject-select"
                            style="width:100%;background:#0f0f14;border:2px solid rgba(122,149,200,0.5);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;cursor:pointer;"
                            onfocus="this.style.borderColor='rgba(122,149,200,0.8)'" onblur="this.style.borderColor='rgba(122,149,200,0.5)'"
                            onchange="updateSubjectHint(this)">
                        <option value="">Select subject…</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" data-name="{{ $subject->name }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    <div class="subject-hint" style="display:flex;align-items:flex-start;gap:8px;background:rgba(122,149,200,0.08);border:1px solid rgba(122,149,200,0.2);border-radius:6px;padding:8px 12px;margin-top:10px;">
                        <span style="color:#7a95c8;font-weight:700;font-size:11px;flex-shrink:0;margin-top:1px;">ℹ</span>
                        <p class="subject-hint-text" style="font-size:11px;color:#a8a39c;margin:0;line-height:1.5;">
                            Subject = teacher specialty. Batch assignment is done manually in Assignments Matrix.
                        </p>
                    </div>
                </div>

                <div>
                    <label style="{{ $LS }}">New Password <span style="color:#6a665f;font-weight:400;text-transform:none;">(leave blank to keep)</span></label>
                    <input type="password" name="password" placeholder="••••••••" style="{{ $IS }}"
                           onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                </div>
            </div>
            <div style="padding:16px 24px;border-top:1px solid rgba(245,241,232,0.08);display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:11px;color:#6a665f;">* Required fields</span>
                <div style="display:flex;gap:10px;">
                    <button type="button" onclick="document.getElementById('editModal').style.display='none'"
                            style="padding:10px 16px;background:#14141b;border:1px solid rgba(245,241,232,0.15);border-radius:6px;font-size:13px;color:#a8a39c;cursor:pointer;">
                        Cancel
                    </button>
                    <button type="submit"
                            style="padding:10px 16px;background:#7a95c8;color:#14141b;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Show/hide subject row based on role
function handleRoleChange(select) {
    var modal    = select.closest('[data-modal]') || select.closest('form').parentElement.parentElement;
    var subjRow  = modal.querySelector ? modal.querySelector('.subject-row') : null;
    // Fallback: find next .subject-row sibling in same form
    if (!subjRow) {
        var form = select.closest('form');
        subjRow  = form ? form.querySelector('.subject-row') : null;
    }
    if (subjRow) {
        var isTeacher = select.value === 'teacher';
        subjRow.style.display = isTeacher ? 'block' : 'none';
        var subjSel = subjRow.querySelector('select');
        if (subjSel) subjSel.required = isTeacher;
    }
}

// Update the info hint text when subject changes
function updateSubjectHint(select) {
    // Text is static — no dynamic update needed; function kept for onchange compatibility
}

function openEditModal(member) {
    document.getElementById('editForm').action  = '/admin/staff/' + member.id;
    document.getElementById('edit_name').value     = member.name     || '';
    document.getElementById('edit_email').value    = member.email    || '';
    document.getElementById('edit_username').value = member.username || '';
    document.getElementById('edit_phone').value    = member.phone    || '';
    document.getElementById('edit_role').value     = member.role     || '';

    // Show/hide subject row
    var subjRow = document.getElementById('editSubjectRow');
    var isTeacher = member.role === 'teacher';
    subjRow.style.display = isTeacher ? 'block' : 'none';

    // Set subject value
    var subjSel = document.getElementById('edit_subject');
    subjSel.value    = member.primary_subject_id || '';
    subjSel.required = isTeacher;
    updateSubjectHint(subjSel);

    document.getElementById('editModal').style.display = 'flex';
}

// Close modals on backdrop click
['addModal','editModal'].forEach(function(id) {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});

// Re-open on validation error
@if($errors->any())
document.getElementById('addModal').style.display = 'flex';
@endif
</script>
@endsection
