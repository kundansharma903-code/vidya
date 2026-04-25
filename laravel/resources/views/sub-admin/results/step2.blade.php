@extends('layouts.sub-admin')
@section('title', 'Upload Results — Upload File')
@section('breadcrumb', 'Upload OMR File')

@section('content')
@php
$routeExists = function(string $name): bool {
    try { route($name); return true; } catch (\Exception $e) { return false; }
};
@endphp
<div style="max-width:960px;">

    {{-- Page heading --}}
    <div style="margin-bottom:24px;">
        <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px;">Upload OMR Responses</h1>
        <p style="font-size:14px;color:#a8a39c;margin:0;">
            Step 2 of 4 — {{ $validation ? 'Reviewing OMR scan output against test records' : 'Upload the Excel/CSV file containing student responses' }}
        </p>
    </div>

    {{-- Stepper --}}
    @include('sub-admin.results._stepper', ['currentStep' => 2])

    {{-- Selected test card --}}
    <div style="background:#14141b;border:1px solid rgba(122,149,200,0.3);border-radius:10px;padding:16px 20px;display:flex;align-items:center;gap:20px;margin-bottom:20px;">
        <div style="width:10px;height:10px;border-radius:50%;background:#7a95c8;flex-shrink:0;"></div>
        <div style="flex:1;min-width:0;">
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:4px;">
                <span style="background:#0f0f14;border-radius:4px;padding:3px 8px;font-size:11px;font-weight:500;color:#a8a39c;">{{ $test->test_code }}</span>
                <span style="font-size:15px;font-weight:600;color:#f5f1e8;">{{ $test->name }}</span>
                @if ($test->course_label)
                    <span style="background:rgba(122,149,200,0.12);border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:500;color:#7a95c8;">{{ $test->course_label }}</span>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:12px;color:#a8a39c;">{{ $test->batch_count }} {{ Str::plural('batch', $test->batch_count) }}</span>
                <span style="color:#6a665f;">·</span>
                <span style="font-size:12px;color:#a8a39c;">{{ $test->student_count }} students</span>
                <span style="color:#6a665f;">·</span>
                <span style="font-size:12px;color:#a8a39c;">{{ \Carbon\Carbon::parse($test->test_date)->format('d M Y') }}</span>
            </div>
        </div>
        <a href="{{ route('sub-admin.results.upload') }}" style="font-size:12px;color:#7a95c8;text-decoration:none;flex-shrink:0;">Change test →</a>
    </div>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div style="background:rgba(200,112,100,0.1);border:1px solid rgba(200,112,100,0.3);border-radius:6px;padding:12px 16px;margin-bottom:20px;">
            @foreach ($errors->all() as $error)
                <p style="font-size:13px;color:#c87064;margin:0;">⚠ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if (!$validation)
        {{-- ===== STATE 1: Upload form ===== --}}

        {{-- Format guide --}}
        <div style="background:rgba(122,149,200,0.06);border:1px solid rgba(122,149,200,0.15);border-radius:6px;padding:12px 16px;display:flex;align-items:flex-start;gap:10px;margin-bottom:20px;">
            <span style="color:#7a95c8;font-size:15px;font-weight:700;flex-shrink:0;margin-top:1px;">ℹ</span>
            <div>
                <p style="font-size:12px;color:#a8a39c;margin:0 0 6px;line-height:1.6;">Expected file format: <strong style="color:#f5f1e8;">Excel (.xlsx / .xls) or CSV</strong></p>
                <p style="font-size:12px;color:#6a665f;margin:0;line-height:1.6;">
                    Row 1: Header row (Roll Number, Q1, Q2, … or any labels)<br>
                    Row 2+: Student roll number in Column A, followed by answers (A / B / C / D or blank for unattempted)
                </p>
            </div>
        </div>

        {{-- Drag-drop upload zone --}}
        <form method="POST" action="{{ route('sub-admin.results.upload.process', $test->id) }}"
              enctype="multipart/form-data" id="uploadForm">
            @csrf

            <div id="dropZone"
                 onclick="document.getElementById('fileInput').click()"
                 ondragover="event.preventDefault();this.style.borderColor='rgba(122,149,200,0.6)';this.style.background='rgba(122,149,200,0.06)'"
                 ondragleave="this.style.borderColor='rgba(245,241,232,0.12)';this.style.background='#14141b'"
                 ondrop="handleDrop(event)"
                 style="background:#14141b;border:2px dashed rgba(245,241,232,0.12);border-radius:10px;padding:64px 20px;text-align:center;cursor:pointer;transition:border-color 0.15s,background 0.15s;margin-bottom:20px;">

                <div style="width:52px;height:52px;background:#1a1a24;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:22px;">
                    📁
                </div>

                <p style="font-size:15px;font-weight:600;color:#f5f1e8;margin:0 0 6px;">Drop your OMR file here</p>
                <p style="font-size:13px;color:#a8a39c;margin:0 0 20px;">or click to browse your computer</p>

                <div id="fileLabel"
                     style="display:inline-block;background:#1a1a24;border:1px solid rgba(245,241,232,0.12);border-radius:6px;padding:9px 18px;font-size:13px;font-weight:500;color:#a8a39c;">
                    Choose file
                </div>

                <input type="file" id="fileInput" name="omr_file" accept=".xlsx,.xls,.csv"
                       style="display:none;" onchange="handleFileSelect(this)">
                <p style="font-size:11px;color:#6a665f;margin:16px 0 0;">Supported: .xlsx, .xls, .csv · Max 10 MB</p>
            </div>

            {{-- Selected file preview (hidden until file chosen) --}}
            <div id="filePreview" style="display:none;background:#14141b;border:1px solid rgba(127,182,133,0.3);border-radius:8px;padding:14px 18px;display:none;align-items:center;gap:14px;margin-bottom:20px;">
                <span style="font-size:22px;">📄</span>
                <div style="flex:1;min-width:0;">
                    <p id="previewName" style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></p>
                    <p id="previewSize" style="font-size:11px;color:#7fb685;margin:0;"></p>
                </div>
                <button type="button" onclick="clearFile()" style="background:none;border:none;font-size:12px;color:#6a665f;cursor:pointer;padding:4px 8px;">✕ Remove</button>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:4px;">
                <a href="{{ route('sub-admin.results.upload') }}"
                   style="font-size:13px;color:#6a665f;text-decoration:none;">← Back to Select Test</a>
                <div style="display:flex;gap:10px;align-items:center;">
                    <a href="{{ route('sub-admin.dashboard') }}"
                       style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:10px 16px;font-size:13px;font-weight:500;color:#a8a39c;text-decoration:none;">
                        Cancel
                    </a>
                    <button type="submit" id="uploadBtn" disabled
                            style="background:#7a95c8;border:none;border-radius:6px;padding:10px 20px;font-size:13px;font-weight:600;color:#14141b;cursor:not-allowed;opacity:0.45;display:flex;align-items:center;gap:8px;">
                        <span id="uploadBtnText">Upload & Validate</span>
                        <span style="font-weight:700;">→</span>
                    </button>
                </div>
            </div>
        </form>

    @else
        {{-- ===== STATE 2: Validation results ===== --}}
        @php
            $v          = $validation;
            $hasUnmatch = count($v['unmatched']) > 0;
            $matchRate  = $v['match_rate'];
            $rateColor  = $matchRate >= 90 ? '#7fb685' : ($matchRate >= 70 ? '#d4a574' : '#c87064');
        @endphp

        {{-- File banner --}}
        <div style="background:#14141b;border:1px solid rgba(127,182,133,0.3);border-radius:8px;padding:14px 18px;display:flex;align-items:center;gap:14px;margin-bottom:20px;">
            <span style="font-size:22px;">📄</span>
            <div style="flex:1;min-width:0;">
                <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $v['file_name'] }}</p>
                <p style="font-size:11px;color:#7fb685;margin:0;">{{ number_format($v['file_size'] / 1024, 1) }} KB · Uploaded successfully</p>
            </div>
            <form method="POST" action="{{ route('sub-admin.results.upload.process', $test->id) }}"
                  enctype="multipart/form-data" id="replaceForm" style="display:inline;">
                @csrf
                <input type="file" id="replaceInput" name="omr_file" accept=".xlsx,.xls,.csv"
                       style="display:none;" onchange="document.getElementById('replaceForm').submit()">
                <button type="button" onclick="document.getElementById('replaceInput').click()"
                        style="background:none;border:1px solid rgba(245,241,232,0.12);border-radius:6px;padding:7px 14px;font-size:12px;font-weight:500;color:#a8a39c;cursor:pointer;white-space:nowrap;">
                    Replace file
                </button>
            </form>
        </div>

        {{-- Validation summary card --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;margin-bottom:20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Validation Summary</p>

            {{-- Stats row --}}
            <div style="display:flex;align-items:center;gap:24px;flex-wrap:wrap;margin-bottom:24px;">
                {{-- Match rate circle --}}
                <div style="position:relative;width:72px;height:72px;flex-shrink:0;">
                    <svg width="72" height="72" viewBox="0 0 72 72">
                        <circle cx="36" cy="36" r="30" fill="none" stroke="rgba(245,241,232,0.06)" stroke-width="6"/>
                        <circle cx="36" cy="36" r="30" fill="none" stroke="{{ $rateColor }}" stroke-width="6"
                                stroke-dasharray="{{ round($matchRate * 188.5 / 100) }} 188.5"
                                stroke-linecap="round" transform="rotate(-90 36 36)"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                        <span style="font-size:14px;font-weight:700;color:{{ $rateColor }};">{{ $matchRate }}%</span>
                    </div>
                </div>

                <div style="flex:1;display:flex;gap:20px;flex-wrap:wrap;">
                    <div>
                        <p style="font-size:22px;font-weight:700;color:#f5f1e8;margin:0 0 2px;letter-spacing:-0.44px;">{{ $v['total'] }}</p>
                        <p style="font-size:11px;color:#6a665f;margin:0;">responses detected</p>
                    </div>
                    <div style="width:1px;background:rgba(245,241,232,0.06);align-self:stretch;"></div>
                    <div>
                        <p style="font-size:22px;font-weight:700;color:#7fb685;margin:0 0 2px;letter-spacing:-0.44px;">{{ $v['matched'] }} ✓</p>
                        <p style="font-size:11px;color:#6a665f;margin:0;">matched</p>
                    </div>
                    <div style="width:1px;background:rgba(245,241,232,0.06);align-self:stretch;"></div>
                    <div>
                        <p style="font-size:22px;font-weight:700;color:#d4a574;margin:0 0 2px;letter-spacing:-0.44px;">{{ count($v['unmatched']) }} ⚠</p>
                        <p style="font-size:11px;color:#6a665f;margin:0;">unmatched rolls</p>
                    </div>
                    <div style="width:1px;background:rgba(245,241,232,0.06);align-self:stretch;"></div>
                    <div>
                        <p style="font-size:22px;font-weight:700;color:#a8a39c;margin:0 0 2px;letter-spacing:-0.44px;">{{ $v['absent_count'] }} ✕</p>
                        <p style="font-size:11px;color:#6a665f;margin:0;">absent</p>
                    </div>
                </div>
            </div>

            {{-- Batch breakdown --}}
            @if (!empty($v['batch_breakdown']))
                <div style="border-top:1px solid rgba(245,241,232,0.06);padding-top:16px;">
                    <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 12px;">Batch Distribution</p>
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach ($v['batch_breakdown'] as $b)
                            @php
                                $pct  = $b['total'] > 0 ? round($b['matched'] / $b['total'] * 100) : 0;
                                $bClr = $b['absent'] === 0 ? '#7fb685' : ($pct >= 80 ? '#d4a574' : '#c87064');
                            @endphp
                            <div>
                                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px;">
                                    <span style="font-size:13px;font-weight:500;color:#f5f1e8;">{{ $b['name'] }}</span>
                                    <div style="display:flex;align-items:center;gap:12px;">
                                        @if ($b['absent'] === 0)
                                            <span style="font-size:11px;color:#7fb685;font-weight:500;">{{ $b['matched'] }}/{{ $b['total'] }} Complete</span>
                                        @else
                                            <span style="font-size:11px;color:#d4a574;font-weight:500;">{{ $b['matched'] }}/{{ $b['total'] }} · {{ $b['absent'] }} absent</span>
                                        @endif
                                        <span style="font-size:11px;font-weight:600;color:{{ $bClr }};min-width:36px;text-align:right;">{{ $pct }}%</span>
                                    </div>
                                </div>
                                <div style="height:5px;background:rgba(245,241,232,0.06);border-radius:3px;overflow:hidden;">
                                    <div style="height:100%;background:{{ $bClr }};border-radius:3px;width:{{ $pct }}%;transition:width 0.4s;"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Unmatched rolls --}}
        @if ($hasUnmatch)
            <div style="margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px;">
                    <p style="font-size:14px;font-weight:600;color:#d4a574;margin:0;">⚠ Unmatched Rolls — Action Required</p>
                    <span style="background:rgba(212,165,116,0.15);border-radius:9999px;padding:2px 10px;font-size:11px;font-weight:500;color:#d4a574;">{{ count($v['unmatched']) }}</span>
                </div>

                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach ($v['unmatched'] as $i => $u)
                        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-left:3px solid #d4a574;border-radius:8px;padding:16px 18px;">
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                                <span style="background:#1a1a24;border-radius:4px;padding:4px 10px;font-size:12px;font-weight:600;color:#d4a574;font-family:monospace;">{{ $u['roll'] }}</span>
                                <span style="font-size:12px;color:#6a665f;">not found in enrolled students</span>
                            </div>

                            @if ($u['suggested'])
                                <div style="background:#1a1a24;border-radius:6px;padding:10px 14px;display:flex;align-items:center;gap:12px;margin-bottom:10px;">
                                    <div style="flex:1;">
                                        <p style="font-size:11px;color:#6a665f;margin:0 0 2px;text-transform:uppercase;letter-spacing:0.88px;">Best match</p>
                                        <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0;">
                                            {{ $u['suggested']['name'] }}
                                            <span style="font-size:11px;color:#a8a39c;margin-left:6px;font-family:monospace;">{{ $u['suggested']['roll_number'] }}</span>
                                        </p>
                                    </div>
                                    <button type="button"
                                            style="background:#7a95c8;border:none;border-radius:6px;padding:7px 14px;font-size:12px;font-weight:600;color:#14141b;cursor:pointer;white-space:nowrap;">
                                        Map to this
                                    </button>
                                </div>
                            @else
                                <div style="background:#1a1a24;border-radius:6px;padding:10px 14px;margin-bottom:10px;">
                                    <p style="font-size:12px;color:#6a665f;margin:0;">No close match found in enrolled students.</p>
                                </div>
                            @endif

                            <div style="display:flex;gap:10px;">
                                <button type="button"
                                        style="background:none;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:6px 12px;font-size:12px;color:#a8a39c;cursor:pointer;">
                                    Browse all students
                                </button>
                                <button type="button"
                                        style="background:none;border:none;padding:6px 12px;font-size:12px;color:#6a665f;cursor:pointer;">
                                    Skip (mark as absent)
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Footer --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:4px;">
            <a href="{{ route('sub-admin.results.upload') }}"
               style="font-size:13px;color:#6a665f;text-decoration:none;">← Back to Select Test</a>
            <div style="display:flex;gap:10px;align-items:center;">
                <a href="{{ route('sub-admin.dashboard') }}"
                   style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:10px 16px;font-size:13px;font-weight:500;color:#a8a39c;text-decoration:none;">
                    Cancel
                </a>
                @if ($hasUnmatch)
                    <a href="{{ route('sub-admin.results.upload.map', $test->id) }}"
                       style="background:#d4a574;border:none;border-radius:6px;padding:10px 20px;font-size:13px;font-weight:600;color:#14141b;display:flex;align-items:center;gap:8px;text-decoration:none;">
                        Resolve Unmatched <span style="font-weight:700;">→</span>
                    </a>
                @else
                    <a href="{{ route('sub-admin.results.upload.analyze', $test->id) }}"
                       style="background:#7fb685;border:none;border-radius:6px;padding:10px 20px;font-size:13px;font-weight:600;color:#14141b;display:flex;align-items:center;gap:8px;text-decoration:none;">
                        Continue to Analyze <span style="font-weight:700;">→</span>
                    </a>
                @endif
            </div>
        </div>

    @endif

</div>

<script>
function handleFileSelect(input) {
    if (input.files.length > 0) showFilePreview(input.files[0]);
}

function handleDrop(e) {
    e.preventDefault();
    const zone = document.getElementById('dropZone');
    zone.style.borderColor = 'rgba(245,241,232,0.12)';
    zone.style.background  = '#14141b';
    const file = e.dataTransfer.files[0];
    if (file) {
        document.getElementById('fileInput').files = e.dataTransfer.files;
        showFilePreview(file);
    }
}

function showFilePreview(file) {
    const allowed = ['xlsx','xls','csv'];
    const ext = file.name.split('.').pop().toLowerCase();
    if (!allowed.includes(ext)) {
        alert('Only .xlsx, .xls, and .csv files are allowed.');
        return;
    }
    document.getElementById('previewName').textContent = file.name;
    document.getElementById('previewSize').textContent = (file.size / 1024).toFixed(1) + ' KB';
    document.getElementById('filePreview').style.display = 'flex';
    document.getElementById('dropZone').style.display    = 'none';

    const btn = document.getElementById('uploadBtn');
    btn.disabled      = false;
    btn.style.opacity = '1';
    btn.style.cursor  = 'pointer';
}

function clearFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').style.display = 'none';
    document.getElementById('dropZone').style.display    = 'block';
    const btn = document.getElementById('uploadBtn');
    btn.disabled      = true;
    btn.style.opacity = '0.45';
    btn.style.cursor  = 'not-allowed';
}

// Show spinner on submit
document.getElementById('uploadForm')?.addEventListener('submit', function() {
    const btn  = document.getElementById('uploadBtn');
    const text = document.getElementById('uploadBtnText');
    btn.disabled      = true;
    btn.style.opacity = '0.7';
    text.textContent  = 'Uploading…';
});
</script>

@endsection
