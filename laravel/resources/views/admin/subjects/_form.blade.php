@php
$inputStyle = "width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;";
$labelStyle = "display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;";
@endphp

<div>
    <label style="{{ $labelStyle }}">Subject Name</label>
    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Physics"
           style="{{ $inputStyle }}"
           onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
           onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    @error('name')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Code <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(1 letter)</span></label>
        <input type="text" name="code" value="{{ old('code') }}" required maxlength="1" placeholder="P"
               style="{{ $inputStyle }}text-transform:uppercase;"
               oninput="this.value=this.value.toUpperCase()"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('code')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $labelStyle }}">Display Order</label>
        <input type="number" name="display_order" value="{{ old('display_order', 0) }}" required
               min="0" placeholder="1"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('display_order')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
</div>

<div>
    <label style="{{ $labelStyle }}">Exam Type</label>
    <select name="exam_type" required style="{{ $inputStyle }}cursor:pointer;"
            onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
            onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        <option value="">Select exam type…</option>
        <option value="BOTH" {{ old('exam_type') === 'BOTH' ? 'selected' : '' }}>NEET + JEE (Both)</option>
        <option value="NEET" {{ old('exam_type') === 'NEET' ? 'selected' : '' }}>NEET only</option>
        <option value="JEE"  {{ old('exam_type') === 'JEE'  ? 'selected' : '' }}>JEE only</option>
    </select>
    @error('exam_type')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>
