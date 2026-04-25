@extends('layouts.sub-admin')
@section('title', 'Upload Results — Resolve Unmatched')
@section('breadcrumb', 'Resolve Unmatched Rolls')

@section('content')
@php
$routeExists = function(string $name): bool {
    try { route($name); return true; } catch (\Exception $e) { return false; }
};
@endphp
<div style="max-width:960px;">

    {{-- Page heading --}}
    <div style="margin-bottom:24px;">
        <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px;">Resolve Unmatched Rolls</h1>
        <p style="font-size:14px;color:#a8a39c;margin:0;">Step 3 of 4 — Map or skip rolls not found in enrolled students</p>
    </div>

    {{-- Stepper --}}
    @include('sub-admin.results._stepper', ['currentStep' => 3])

    {{-- Selected test card --}}
    <div style="background:#14141b;border:1px solid rgba(122,149,200,0.3);border-radius:10px;padding:14px 20px;display:flex;align-items:center;gap:20px;margin-bottom:20px;">
        <div style="width:10px;height:10px;border-radius:50%;background:#7a95c8;flex-shrink:0;"></div>
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:3px;">
                <span style="background:#0f0f14;border-radius:4px;padding:3px 8px;font-size:11px;font-weight:500;color:#a8a39c;">{{ $test->test_code }}</span>
                <span style="font-size:15px;font-weight:600;color:#f5f1e8;">{{ $test->name }}</span>
            </div>
            <span style="font-size:12px;color:#a8a39c;">{{ $test->batch_count }} {{ Str::plural('batch', $test->batch_count) }} · {{ $test->student_count }} students</span>
        </div>
        <div style="background:rgba(212,165,116,0.12);border-radius:9999px;padding:5px 12px;font-size:11px;font-weight:500;color:#d4a574;flex-shrink:0;">
            {{ count($validation['unmatched']) }} unmatched
        </div>
    </div>

    {{-- Info --}}
    <div style="background:rgba(212,165,116,0.08);border:1px solid rgba(212,165,116,0.2);border-radius:6px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:20px;">
        <span style="color:#d4a574;font-size:15px;font-weight:700;flex-shrink:0;">⚠</span>
        <p style="font-size:12px;color:#a8a39c;margin:0;line-height:1.6;">
            These roll numbers were in the uploaded file but not found in enrolled students. Map each to the correct student, or skip to mark them absent.
        </p>
    </div>

    <form method="POST" action="{{ route('sub-admin.results.upload.save-map', $test->id) }}">
        @csrf

        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px;">
            @foreach ($validation['unmatched'] as $u)
                @php $roll = $u['roll']; $resolved = $resolved[$roll] ?? null; @endphp
                <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-left:3px solid #d4a574;border-radius:8px;padding:18px 20px;">

                    {{-- Roll header --}}
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                        <span style="background:#1a1a24;border-radius:4px;padding:4px 12px;font-size:13px;font-weight:700;color:#d4a574;font-family:monospace;">{{ $roll }}</span>
                        <span style="font-size:12px;color:#6a665f;">not found in enrolled students</span>
                    </div>

                    {{-- Option 1: Map to suggested --}}
                    @if ($u['suggested'])
                        <label style="display:flex;align-items:center;gap:12px;background:#1a1a24;border:1px solid rgba(245,241,232,0.06);border-radius:6px;padding:12px 14px;cursor:pointer;margin-bottom:8px;"
                               id="label_suggest_{{ $loop->index }}">
                            <input type="radio" name="decision[{{ $roll }}]" value="map"
                                   id="radio_suggest_{{ $loop->index }}"
                                   onchange="selectOption('{{ $loop->index }}', 'suggest')"
                                   {{ ($resolved && $resolved['action'] === 'map') ? 'checked' : '' }}
                                   style="accent-color:#7a95c8;width:16px;height:16px;flex-shrink:0;">
                            <input type="hidden" name="mapped_to[{{ $roll }}]" id="mapped_to_{{ $loop->index }}" value="{{ $u['suggested']['id'] }}">
                            <div style="flex:1;">
                                <p style="font-size:11px;color:#6a665f;margin:0 0 2px;text-transform:uppercase;letter-spacing:0.88px;">Best match</p>
                                <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">
                                    {{ $u['suggested']['name'] }}
                                    <span style="font-size:11px;color:#a8a39c;font-weight:400;margin-left:8px;font-family:monospace;">{{ $u['suggested']['roll_number'] }}</span>
                                </p>
                            </div>
                            <span style="font-size:11px;color:#7a95c8;font-weight:500;white-space:nowrap;">Map to this</span>
                        </label>
                    @endif

                    {{-- Option 2: Browse & select --}}
                    <div style="margin-bottom:8px;">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px;">
                            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                                <input type="radio" name="decision[{{ $roll }}]" value="map"
                                       id="radio_browse_{{ $loop->index }}"
                                       onchange="selectOption('{{ $loop->index }}', 'browse')"
                                       style="accent-color:#7a95c8;width:16px;height:16px;">
                                <span style="font-size:13px;color:#a8a39c;">Browse all students</span>
                            </label>
                        </div>
                        <div id="browse_{{ $loop->index }}" style="display:none;margin-left:24px;">
                            <input type="text" placeholder="Search by name or roll number…"
                                   oninput="filterStudents('{{ $loop->index }}', this.value)"
                                   style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:9px 12px;font-size:12px;color:#f5f1e8;outline:none;margin-bottom:6px;">
                            <div id="student_list_{{ $loop->index }}"
                                 style="max-height:180px;overflow-y:auto;background:#0f0f14;border:1px solid rgba(245,241,232,0.08);border-radius:6px;">
                                @foreach ($students as $st)
                                    <div class="student-option-{{ $loop->parent->index }}"
                                         data-name="{{ strtolower($st->name) }}" data-roll="{{ strtolower($st->roll_number) }}"
                                         onclick="pickStudent('{{ $loop->parent->index }}', {{ $st->id }})"
                                         style="padding:9px 12px;cursor:pointer;font-size:12px;color:#a8a39c;border-bottom:1px solid rgba(245,241,232,0.04);"
                                         onmouseover="this.style.background='rgba(122,149,200,0.08)'" onmouseout="this.style.background=''">
                                        <span style="font-family:monospace;color:#6a665f;margin-right:10px;">{{ $st->roll_number }}</span>{{ $st->name }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Option 3: Skip --}}
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:4px 0;">
                        <input type="radio" name="decision[{{ $roll }}]" value="skip"
                               id="radio_skip_{{ $loop->index }}"
                               onchange="selectOption('{{ $loop->index }}', 'skip')"
                               {{ (!$resolved || $resolved['action'] === 'skip') ? 'checked' : '' }}
                               style="accent-color:#6a665f;width:16px;height:16px;">
                        <span style="font-size:13px;color:#6a665f;">Skip — mark as absent</span>
                    </label>
                </div>
            @endforeach
        </div>

        {{-- Footer --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:4px;">
            <a href="{{ route('sub-admin.results.upload.file', $test->id) }}"
               style="font-size:13px;color:#6a665f;text-decoration:none;">← Back to Upload</a>
            <div style="display:flex;gap:10px;align-items:center;">
                <a href="{{ route('sub-admin.dashboard') }}"
                   style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:10px 16px;font-size:13px;font-weight:500;color:#a8a39c;text-decoration:none;">
                    Cancel
                </a>
                <button type="submit"
                        style="background:#7a95c8;border:none;border-radius:6px;padding:10px 20px;font-size:13px;font-weight:600;color:#14141b;cursor:pointer;display:flex;align-items:center;gap:8px;">
                    Continue to Analyze <span style="font-weight:700;">→</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function selectOption(idx, type) {
    document.getElementById('browse_' + idx).style.display = (type === 'browse') ? 'block' : 'none';
}

function filterStudents(idx, query) {
    const q = query.toLowerCase();
    document.querySelectorAll('.student-option-' + idx).forEach(el => {
        const match = el.dataset.name.includes(q) || el.dataset.roll.includes(q);
        el.style.display = match ? 'block' : 'none';
    });
}

function pickStudent(idx, studentId) {
    document.getElementById('mapped_to_' + idx).value = studentId;
    document.getElementById('radio_browse_' + idx).checked = true;
    document.getElementById('browse_' + idx).style.display = 'none';
}
</script>
@endsection
