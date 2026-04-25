@php
$inputStyle = "width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;";
$labelStyle = "display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;";
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Full Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rohan Sharma"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('name')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $labelStyle }}">Batch</label>
        <select name="batch_id" required style="{{ $inputStyle }}cursor:pointer;"
                onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
                onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
            <option value="">Select batch…</option>
            @foreach ($batches as $batch)
                <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                    {{ $batch->name }}
                </option>
            @endforeach
        </select>
        @error('batch_id')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Roll Number</label>
        <input type="text" name="roll_number" value="{{ old('roll_number') }}" required placeholder="e.g. 2401"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('roll_number')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $labelStyle }}">Enrollment No <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="text" name="enrollment_number" value="{{ old('enrollment_number') }}" placeholder="e.g. EN2401"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('enrollment_number')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Phone <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. 9876543210"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('phone')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $labelStyle }}">Parent Phone <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="text" name="parent_phone" value="{{ old('parent_phone') }}" placeholder="e.g. 9812345678"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('parent_phone')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Email <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="e.g. rohan@email.com"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('email')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
    <div>
        <label style="{{ $labelStyle }}">Admission Date <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="date" name="admission_date" value="{{ old('admission_date') }}"
               style="{{ $inputStyle }}color-scheme:dark;"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
        @error('admission_date')<p style="font-size:11px;color:#e05252;margin:4px 0 0 0;">{{ $message }}</p>@enderror
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Gender <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <select name="gender" style="{{ $inputStyle }}cursor:pointer;"
                onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
                onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
            <option value="">—</option>
            <option value="M" {{ old('gender') === 'M' ? 'selected' : '' }}>Male</option>
            <option value="F" {{ old('gender') === 'F' ? 'selected' : '' }}>Female</option>
            <option value="O" {{ old('gender') === 'O' ? 'selected' : '' }}>Other</option>
        </select>
    </div>
    <div>
        <label style="{{ $labelStyle }}">Medium <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <select name="medium" style="{{ $inputStyle }}cursor:pointer;"
                onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
                onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
            <option value="english" {{ old('medium', 'english') === 'english' ? 'selected' : '' }}>English</option>
            <option value="hindi"   {{ old('medium') === 'hindi'   ? 'selected' : '' }}>Hindi</option>
        </select>
    </div>
    <div>
        <label style="{{ $labelStyle }}">Date of Birth <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
               style="{{ $inputStyle }}color-scheme:dark;"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label style="{{ $labelStyle }}">Father's Name <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="text" name="father_name" value="{{ old('father_name') }}" placeholder="e.g. Ramesh Sharma"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    </div>
    <div>
        <label style="{{ $labelStyle }}">Mother's Name <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
        <input type="text" name="mother_name" value="{{ old('mother_name') }}" placeholder="e.g. Sunita Sharma"
               style="{{ $inputStyle }}"
               onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
               onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
    </div>
</div>

<div>
    <label style="{{ $labelStyle }}">Address <span style="color:#6a665f;font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
    <textarea name="address" rows="2" placeholder="e.g. 12, Lal Kothi, Jaipur"
              style="{{ $inputStyle }}resize:vertical;"
              onfocus="this.style.borderColor='rgba(122,149,200,0.4)'"
              onblur="this.style.borderColor='rgba(245,241,232,0.10)'">{{ old('address') }}</textarea>
</div>
