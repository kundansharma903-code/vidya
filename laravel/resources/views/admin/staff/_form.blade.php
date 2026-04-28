@php
$inputStyle = "width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;";
$labelStyle = "display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;";
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Full Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rathore Sir"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('name')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $labelStyle }}">Phone <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. +91 98765 43210"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('phone')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label style="{{ $labelStyle }}">Email</label>
    <input type="email" name="email" value="{{ old('email') }}" required placeholder="e.g. rathore@coaching.com"
           style="{{ $inputStyle }}"
           onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
           onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    @error('email')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Username</label>
        <input type="text" name="username" value="{{ old('username') }}" required placeholder="e.g. rathore_sir"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('username')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $labelStyle }}">Role</label>
        <select name="role" required class="role-select" style="{{ $inputStyle }}cursor:pointer;"
                onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
                onblur="this.style.borderColor='rgba(245,241,232,0.10)'"
                onchange="handleRoleChange(this)">
            <option value="">Select role…</option>
            <option value="owner"         {{ old('role') === 'owner'         ? 'selected' : '' }}>Owner</option>
            <option value="academic_head" {{ old('role') === 'academic_head' ? 'selected' : '' }}>Academic Head</option>
            <option value="admin"         {{ old('role') === 'admin'         ? 'selected' : '' }}>Admin</option>
            <option value="sub_admin"     {{ old('role') === 'sub_admin'     ? 'selected' : '' }}>Sub-Admin</option>
            <option value="teacher"       {{ old('role') === 'teacher'       ? 'selected' : '' }}>Teacher</option>
            <option value="reception"     {{ old('role') === 'reception'     ? 'selected' : '' }}>Reception</option>
        </select>
        @error('role')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Subject field — shown only for Teacher role --}}
<div class="subject-row" style="display:{{ old('role') === 'teacher' ? 'block' : 'none' }};">
    <label style="display:block;font-size:11px;font-weight:500;color:#7a95c8;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;">
        Subject (Required for Teachers) *
    </label>
    <select name="primary_subject_id" class="subject-select"
            style="width:100%;background:#0f0f14;border:2px solid rgba(122,149,200,0.5);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;cursor:pointer;"
            onfocus="this.style.borderColor='rgba(122,149,200,0.8)'"
            onblur="this.style.borderColor='rgba(122,149,200,0.5)'"
            onchange="updateSubjectHint(this)">
        <option value="">Select subject…</option>
        @foreach($subjects as $subject)
            <option value="{{ $subject->id }}"
                    data-name="{{ $subject->name }}"
                    {{ old('primary_subject_id') == $subject->id ? 'selected' : '' }}>
                {{ $subject->name }}
            </option>
        @endforeach
    </select>
    @error('primary_subject_id')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror

    {{-- Info hint --}}
    <div class="subject-hint" style="display:flex;align-items:flex-start;gap:8px;background:rgba(122,149,200,0.08);border:1px solid rgba(122,149,200,0.2);border-radius:6px;padding:8px 12px;margin-top:10px;">
        <span style="color:#7a95c8;font-weight:700;font-size:11px;flex-shrink:0;margin-top:1px;">ℹ</span>
        <p class="subject-hint-text" style="font-size:11px;color:#a8a39c;margin:0;line-height:1.5;">
            Subject = teacher specialty. Batch assignment is done manually in Assignments Matrix.
        </p>
    </div>
</div>

<div>
    <label style="{{ $labelStyle }}">Password <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(min 6 chars)</span></label>
    <input type="password" name="password" placeholder="••••••••"
           style="{{ $inputStyle }}"
           onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
           onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    @error('password')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>
