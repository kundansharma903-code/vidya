@extends('layouts.sub-admin')
@section('title', 'Create Test')
@section('breadcrumb', 'Create Test')

@php
$routeExists = function(string $name): bool {
    try { route($name); return true; } catch (\Exception $e) { return false; }
};
@endphp

@section('content')
<div style="max-width:900px;">

    {{-- Page heading --}}
    <div style="margin-bottom:22px;">
        <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px;">Create New Test</h1>
        <p style="font-size:14px;color:#a8a39c;margin:0;">Just enter test metadata. Topic mapping comes from result Excel later.</p>
    </div>

    {{-- Info banner --}}
    <div style="background:rgba(122,149,200,0.08);border:1px solid rgba(122,149,200,0.3);border-radius:8px;padding:12px 16px;display:flex;align-items:flex-start;gap:10px;margin-bottom:22px;">
        <span style="color:#7a95c8;font-weight:700;font-size:14px;flex-shrink:0;">ℹ</span>
        <p style="font-size:12px;color:#a8a39c;margin:0;line-height:1.6;">
            Vidya analytics-only platform hai. Paper banane ka kaam coaching ka hai. Excel upload se topic mapping auto-detect hoga.
        </p>
    </div>

    @if ($errors->any())
        <div style="background:rgba(224,82,82,0.1);border:1px solid rgba(224,82,82,0.3);border-radius:8px;padding:12px 16px;margin-bottom:22px;">
            @foreach ($errors->all() as $error)
                <p style="font-size:12px;color:#e05252;margin:2px 0;">• {{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Form card --}}
    <form method="POST" action="{{ route('sub-admin.tests.store') }}">
        @csrf

        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">

            {{-- Card header --}}
            <div style="padding:18px 24px;border-bottom:1px solid rgba(245,241,232,0.06);">
                <span style="font-size:15px;font-weight:600;color:#f5f1e8;">Test Details</span>
            </div>

            {{-- Fields --}}
            <div style="padding:24px;display:flex;flex-direction:column;gap:20px;">

                {{-- Row 1: Test Code + Test Name --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">TEST CODE (Auto-generated, editable) *</label>
                        <input type="text" name="test_code" value="{{ old('test_code', $nextCode) }}"
                               style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#f5f1e8;outline:none;"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'" required>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">TEST NAME *</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. NEET Mock Test 7"
                               style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#f5f1e8;outline:none;"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'" required>
                    </div>
                </div>

                {{-- Row 2: Date + Pattern + Test Type --}}
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">TEST DATE *</label>
                        <input type="date" name="test_date" value="{{ old('test_date') }}"
                               style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#f5f1e8;outline:none;color-scheme:dark;"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'" required>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">PATTERN *</label>
                        <select name="pattern" id="patternSelect"
                                style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#f5f1e8;outline:none;appearance:none;cursor:pointer;"
                                onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'" required>
                            <option value="">Select pattern…</option>
                            <option value="NEET 180Q"  {{ old('pattern') === 'NEET 180Q'  ? 'selected' : '' }}>NEET 180Q (Physics + Chem + Bio + Zoo)</option>
                            <option value="JEE 90Q"    {{ old('pattern') === 'JEE 90Q'    ? 'selected' : '' }}>JEE 90Q (Physics + Chem + Maths)</option>
                            <option value="AIIMS 200Q" {{ old('pattern') === 'AIIMS 200Q' ? 'selected' : '' }}>AIIMS 200Q</option>
                            <option value="Custom"     {{ old('pattern') === 'Custom'     ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">TEST TYPE *</label>
                        <select name="test_type"
                                style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#f5f1e8;outline:none;appearance:none;cursor:pointer;"
                                onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'" required>
                            <option value="mock"     {{ old('test_type','mock') === 'mock'     ? 'selected' : '' }}>Mock Test</option>
                            <option value="dpt"      {{ old('test_type') === 'dpt'      ? 'selected' : '' }}>DPT</option>
                            <option value="weekly"   {{ old('test_type') === 'weekly'   ? 'selected' : '' }}>Weekly</option>
                            <option value="flt"      {{ old('test_type') === 'flt'      ? 'selected' : '' }}>Full Length Test</option>
                            <option value="chapter"  {{ old('test_type') === 'chapter'  ? 'selected' : '' }}>Chapter Test</option>
                            <option value="revision" {{ old('test_type') === 'revision' ? 'selected' : '' }}>Revision</option>
                        </select>
                    </div>
                </div>

                {{-- Batches multi-select --}}
                <div>
                    <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">BATCHES (multiple allowed) *</label>
                    <div id="batchBox"
                         style="background:#0f0f14;border:1px solid rgba(122,149,200,0.4);border-radius:6px;padding:8px 14px;min-height:44px;display:flex;flex-wrap:wrap;align-items:center;gap:6px;cursor:pointer;position:relative;"
                         onclick="toggleDropdown('batchDropdown', this)">
                        <div id="batchTags" style="display:flex;flex-wrap:wrap;gap:6px;flex:1;"></div>
                        <span style="color:#6a665f;font-size:11px;flex-shrink:0;">▾</span>

                        <div id="batchDropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#1a1a24;border:1px solid rgba(245,241,232,0.1);border-radius:6px;z-index:100;max-height:200px;overflow-y:auto;">
                            @foreach ($batches as $batch)
                                <label style="display:flex;align-items:center;gap:10px;padding:9px 14px;cursor:pointer;font-size:13px;color:#f5f1e8;"
                                       onmouseover="this.style.background='rgba(122,149,200,0.08)'" onmouseout="this.style.background=''">
                                    <input type="checkbox" name="batch_ids[]" value="{{ $batch->id }}"
                                           class="batch-cb" onchange="syncTags('batch')"
                                           data-label="{{ $batch->name }}"
                                           {{ in_array($batch->id, old('batch_ids', [])) ? 'checked' : '' }}>
                                    {{ $batch->name }}
                                </label>
                            @endforeach
                            @if ($batches->isEmpty())
                                <p style="padding:12px 14px;font-size:12px;color:#6a665f;margin:0;">No active batches found.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Teachers multi-select --}}
                <div>
                    <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">TEACHERS (multi-select) *</label>
                    <div id="teacherBox"
                         style="background:#0f0f14;border:1px solid rgba(122,149,200,0.4);border-radius:6px;padding:8px 14px;min-height:44px;display:flex;flex-wrap:wrap;align-items:center;gap:6px;cursor:pointer;position:relative;"
                         onclick="toggleDropdown('teacherDropdown', this)">
                        <div id="teacherTags" style="display:flex;flex-wrap:wrap;gap:6px;flex:1;"></div>
                        <span style="color:#6a665f;font-size:11px;flex-shrink:0;">▾</span>

                        <div id="teacherDropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#1a1a24;border:1px solid rgba(245,241,232,0.1);border-radius:6px;z-index:100;max-height:200px;overflow-y:auto;">
                            @foreach ($teachers as $teacher)
                                <label style="display:flex;align-items:center;gap:10px;padding:9px 14px;cursor:pointer;font-size:13px;color:#f5f1e8;"
                                       onmouseover="this.style.background='rgba(122,149,200,0.08)'" onmouseout="this.style.background=''">
                                    <input type="checkbox" name="teacher_ids[]" value="{{ $teacher->id }}"
                                           class="teacher-cb" onchange="syncTags('teacher')"
                                           data-label="{{ $teacher->name }}"
                                           {{ in_array($teacher->id, old('teacher_ids', [])) ? 'checked' : '' }}>
                                    {{ $teacher->name }}
                                </label>
                            @endforeach
                            @if ($teachers->isEmpty())
                                <p style="padding:12px 14px;font-size:12px;color:#6a665f;margin:0;">No active teachers found.</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Marking Scheme header --}}
                <div style="border-bottom:1px solid rgba(245,241,232,0.06);padding-bottom:12px;padding-top:8px;">
                    <span style="font-size:11px;font-weight:700;color:#7a95c8;letter-spacing:1.32px;text-transform:uppercase;">Marking Scheme</span>
                </div>

                {{-- Marking scheme fields --}}
                <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;">
                    <div>
                        <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">CORRECT ANSWER *</label>
                        <input type="number" name="correct_marks" value="{{ old('correct_marks', 4) }}" step="0.5" min="0"
                               style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#f5f1e8;outline:none;"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'" required>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">WRONG ANSWER *</label>
                        <input type="number" name="incorrect_marks" value="{{ old('incorrect_marks', -1) }}" step="0.5" max="0"
                               style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#f5f1e8;outline:none;"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'" required>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">NOT ATTEMPTED *</label>
                        <input type="number" name="unattempted_marks" value="{{ old('unattempted_marks', 0) }}" step="0.5"
                               style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#f5f1e8;outline:none;"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'" required>
                    </div>
                    <div>
                        <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.5px;margin-bottom:8px;">INVALID (* in OMR) *</label>
                        <input type="number" name="invalid_marks" value="{{ old('invalid_marks', -1) }}" step="0.5" max="0"
                               style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#f5f1e8;outline:none;"
                               onfocus="this.style.borderColor='rgba(122,149,200,0.5)'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'" required>
                    </div>
                </div>

            </div>

            {{-- Form footer --}}
            <div style="padding:18px 24px;border-top:1px solid rgba(245,241,232,0.06);display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:11px;color:#6a665f;">Test will be saved with status 'Scheduled' — ready for result upload after the test.</span>
                <div style="display:flex;gap:10px;">
                    <a href="{{ $routeExists('sub-admin.tests.index') ? route('sub-admin.tests.index') : '#' }}"
                       style="background:#14141b;border:1px solid rgba(245,241,232,0.15);border-radius:6px;padding:11px 18px;font-size:13px;font-weight:500;color:#a8a39c;text-decoration:none;">
                        Cancel
                    </a>
                    <button type="submit"
                            style="background:#7a95c8;border:none;border-radius:6px;padding:11px 18px;font-size:13px;font-weight:600;color:#14141b;cursor:pointer;display:flex;align-items:center;gap:8px;">
                        <span>✓</span> Create Test
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
function toggleDropdown(id, box) {
    event.stopPropagation();
    const dd = document.getElementById(id);
    const isOpen = dd.style.display === 'block';
    document.querySelectorAll('[id$="Dropdown"]').forEach(d => d.style.display = 'none');
    dd.style.display = isOpen ? 'none' : 'block';
}

document.addEventListener('click', () => {
    document.querySelectorAll('[id$="Dropdown"]').forEach(d => d.style.display = 'none');
});

function syncTags(type) {
    const cbs   = document.querySelectorAll('.' + type + '-cb:checked');
    const tags  = document.getElementById(type + 'Tags');
    tags.innerHTML = '';
    cbs.forEach(cb => {
        const tag = document.createElement('span');
        tag.style.cssText = 'background:rgba(122,149,200,0.18);color:#7a95c8;font-size:11px;font-weight:500;border-radius:4px;padding:4px 6px 4px 8px;display:inline-flex;align-items:center;gap:6px;';
        tag.innerHTML = cb.dataset.label + ' <span style="cursor:pointer;font-size:10px;font-weight:700;" onclick="removeTag(\'' + type + '\',' + cb.value + ')">✕</span>';
        tags.appendChild(tag);
    });
}

function removeTag(type, val) {
    const cb = document.querySelector('.' + type + '-cb[value="' + val + '"]');
    if (cb) { cb.checked = false; syncTags(type); }
}

// Init tags from old() values on page load
document.addEventListener('DOMContentLoaded', () => {
    syncTags('batch');
    syncTags('teacher');
});
</script>
@endsection
