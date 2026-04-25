@php $edit = $edit ?? false; @endphp

@php
$inputStyle = "width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;";
$labelStyle = "display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;";
@endphp

{{-- Name --}}
<div>
    <label style="{{ $labelStyle }}">Course Name</label>
    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. NEET-2026"
           style="{{ $inputStyle }}"
           onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
           onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    @error('name')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>

{{-- Code --}}
<div>
    <label style="{{ $labelStyle }}">Code</label>
    <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. NEET-26"
           style="{{ $inputStyle }}"
           onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
           onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    @error('code')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>

{{-- Exam Type --}}
<div>
    <label style="{{ $labelStyle }}">Exam Type</label>
    <select name="exam_type" required style="{{ $inputStyle }}cursor:pointer;"
            onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
            onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        <option value="">Select exam type…</option>
        <option value="NEET"         {{ old('exam_type') === 'NEET'         ? 'selected' : '' }}>NEET</option>
        <option value="JEE_MAIN"     {{ old('exam_type') === 'JEE_MAIN'     ? 'selected' : '' }}>JEE-Main</option>
        <option value="JEE_ADVANCED" {{ old('exam_type') === 'JEE_ADVANCED' ? 'selected' : '' }}>JEE-Advanced</option>
        <option value="OTHER"        {{ old('exam_type') === 'OTHER'        ? 'selected' : '' }}>Other</option>
    </select>
    @error('exam_type')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>

{{-- Target Year + Duration --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Target Year</label>
        <input type="number" name="target_year" value="{{ old('target_year') }}" required
               min="2024" max="2035" placeholder="2026"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('target_year')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $labelStyle }}">Duration (months)</label>
        <input type="number" name="duration_months" value="{{ old('duration_months') }}" required
               min="1" max="60" placeholder="12"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('duration_months')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
</div>

{{-- Total Questions --}}
<div>
    <label style="{{ $labelStyle }}">Total Questions <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
    <input type="number" name="total_questions" value="{{ old('total_questions') }}"
           min="1" placeholder="e.g. 180"
           style="{{ $inputStyle }}"
           onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
           onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    @error('total_questions')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
</div>
