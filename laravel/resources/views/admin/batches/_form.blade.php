@php
$inputStyle = "width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;";
$labelStyle = "display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;";
@endphp

<div>
    <label style="{{ $labelStyle }}">Batch Name</label>
    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Morning-Batch-A"
           style="{{ $inputStyle }}"
           onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
           onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    @error('name')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Code <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="text" name="code" value="{{ old('code') }}" placeholder="e.g. NEET-M-A"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('code')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $labelStyle }}">Capacity</label>
        <input type="number" name="capacity" value="{{ old('capacity', 50) }}" required min="1" max="1000"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('capacity')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label style="{{ $labelStyle }}">Course</label>
    <select name="course_id" required style="{{ $inputStyle }}cursor:pointer;"
            onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
            onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        <option value="">Select course…</option>
        @foreach ($courses as $course)
            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                {{ $course->name }}
            </option>
        @endforeach
    </select>
    @error('course_id')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>

<div>
    <label style="{{ $labelStyle }}">Timing <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
    <input type="text" name="timing_label" value="{{ old('timing_label') }}" placeholder="e.g. 8 AM – 1 PM"
           style="{{ $inputStyle }}"
           onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
           onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    @error('timing_label')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Start Date <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="date" name="start_date" value="{{ old('start_date') }}"
               style="{{ $inputStyle }}color-scheme:dark;"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('start_date')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $labelStyle }}">End Date <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="date" name="end_date" value="{{ old('end_date') }}"
               style="{{ $inputStyle }}color-scheme:dark;"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('end_date')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
</div>
