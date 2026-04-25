@extends('layouts.admin')
@section('title', 'Assignments')

@section('content')
@php
use App\Http\Controllers\Admin\AssignmentController;

$avatarPalette = ['#5f7eb4','#7fb685','#a392c8','#d4a574','#6ab0b2','#c87064'];

// Subject badge color based on code letter
$subjectColors = [
    'P' => ['bg'=>'rgba(95,126,180,0.15)',  'color'=>'#5f7eb4'],
    'C' => ['bg'=>'rgba(127,182,133,0.15)', 'color'=>'#7fb685'],
    'B' => ['bg'=>'rgba(163,146,200,0.15)', 'color'=>'#a392c8'],
    'Z' => ['bg'=>'rgba(200,112,100,0.15)', 'color'=>'#c87064'],
    'M' => ['bg'=>'rgba(212,165,116,0.15)', 'color'=>'#d4a574'],
    'E' => ['bg'=>'rgba(106,176,178,0.15)', 'color'=>'#6ab0b2'],
];

// Course badge color
$courseBadge = function($examType) {
    if ($examType === 'NEET')                              return ['bg'=>'rgba(127,182,133,0.12)','color'=>'#7fb685'];
    if (in_array($examType, ['JEE_MAIN','JEE_ADVANCED'])) return ['bg'=>'rgba(163,146,200,0.12)','color'=>'#a392c8'];
    return ['bg'=>'rgba(245,241,232,0.06)','color'=>'#a8a39c'];
};
@endphp

<div style="padding:32px 36px;max-width:1400px;margin:0 auto;">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;">
        <div>
            <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;margin:0 0 6px 0;letter-spacing:-0.56px;">Teacher ↔ Batch Assignments</h1>
            <div style="display:flex;align-items:center;gap:10px;font-size:13px;">
                <span style="font-weight:500;color:#f5f1e8;">{{ $teachers->count() }} teachers</span>
                <span style="color:#6a665f;">·</span>
                <span style="color:#a8a39c;">{{ $batches->count() }} batches</span>
                <span style="color:#6a665f;">·</span>
                <span style="color:#7fb685;">{{ $assignmentCount }} assignments</span>
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;">
            <button style="display:flex;align-items:center;gap:8px;background:#14141b;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;font-weight:500;color:#f5f1e8;cursor:pointer;">
                <span style="color:#a8a39c;">↓</span> Export CSV
            </button>
            <button onclick="document.getElementById('bulkModal').style.display='flex'"
                    style="display:flex;align-items:center;gap:6px;background:#7a95c8;color:#14141b;border:none;border-radius:6px;padding:10px 16px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 6px rgba(122,149,200,0.2);">
                <span style="font-size:14px;font-weight:700;">+</span> Bulk Assign
            </button>
        </div>
    </div>

    {{-- Legend --}}
    <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px;">
        <div style="display:flex;align-items:center;gap:6px;">
            <div style="width:16px;height:16px;background:rgba(122,149,200,0.2);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#7a95c8;">✓</div>
            <span style="font-size:12px;color:#a8a39c;">Assigned</span>
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <div style="width:16px;height:16px;background:rgba(106,102,95,0.2);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#6a665f;">○</div>
            <span style="font-size:12px;color:#a8a39c;">Not assigned</span>
        </div>
        <div style="display:flex;align-items:center;gap:6px;">
            <div style="width:16px;height:16px;background:rgba(106,102,95,0.06);border-radius:4px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#6a665f;">—</div>
            <span style="font-size:12px;color:#a8a39c;">Not applicable (subject mismatch)</span>
        </div>
        <div style="flex:1;"></div>
        <span style="font-size:12px;color:#6a665f;">Click any cell to toggle assignment</span>
    </div>

    @if($teachers->isEmpty() || $batches->isEmpty())
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:60px 24px;text-align:center;">
        <p style="font-size:14px;color:#a8a39c;margin:0 0 6px;">
            @if($teachers->isEmpty()) No active teachers found.
            @else No active batches found.
            @endif
        </p>
        <p style="font-size:12px;color:#6a665f;margin:0;">Add teachers and batches first to manage assignments.</p>
    </div>
    @else

    {{-- Matrix --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;overflow:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:max-content;">
            {{-- Header --}}
            <thead>
                <tr style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);">
                    <th style="padding:14px 24px 14px 24px;text-align:left;font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.2px;text-transform:uppercase;width:260px;white-space:nowrap;">Teacher</th>
                    @foreach($batches as $batch)
                    @php $cb = $courseBadge($batch->course_exam_type); @endphp
                    <th style="padding:14px 8px;width:140px;min-width:140px;border-left:1px solid rgba(245,241,232,0.05);">
                        <div style="display:flex;flex-direction:column;align-items:center;gap:6px;">
                            <span style="font-size:12px;font-weight:600;color:#f5f1e8;white-space:nowrap;">{{ $batch->code ?: Str::limit($batch->name, 10) }}</span>
                            <span style="display:inline-flex;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:500;background:{{ $cb['bg'] }};color:{{ $cb['color'] }};white-space:nowrap;">
                                {{ $batch->course_name }}
                            </span>
                        </div>
                    </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($teachers as $teacher)
                @php
                    $avIdx    = ord(strtoupper($teacher->name[0])) % count($avatarPalette);
                    $avColor  = $avatarPalette[$avIdx];
                    $initials = implode('', array_map(fn($w) => strtoupper($w[0]), array_slice(explode(' ', trim($teacher->name)), 0, 2)));
                    $subj     = $teacherSubjects[$teacher->id] ?? null;
                    $subjCode = $subj ? strtoupper($subj->subject_code) : null;
                    $sc       = $subjectColors[$subjCode] ?? ['bg'=>'rgba(245,241,232,0.06)','color'=>'#a8a39c'];
                @endphp
                <tr style="border-bottom:1px solid rgba(245,241,232,0.05);"
                    onmouseover="this.cells[0].style.background='rgba(245,241,232,0.02)'"
                    onmouseout="this.cells[0].style.background='transparent'">

                    {{-- Teacher cell --}}
                    <td style="padding:0 16px 0 24px;height:72px;width:260px;">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:36px;height:36px;border-radius:50%;background:{{ $avColor }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#f5f1e8;flex-shrink:0;">
                                {{ $initials }}
                            </div>
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                <span style="font-size:13px;font-weight:500;color:#f5f1e8;white-space:nowrap;">{{ $teacher->name }}</span>
                                @if($subj)
                                <span style="display:inline-flex;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:500;background:{{ $sc['bg'] }};color:{{ $sc['color'] }};width:fit-content;">
                                    {{ $subj->subject_name }}
                                </span>
                                @else
                                <span style="font-size:10px;color:#6a665f;">No subject</span>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Batch cells --}}
                    @foreach($batches as $batch)
                    @php
                        $applicable = AssignmentController::isApplicable($subj->subject_exam_type ?? null, $batch->course_exam_type);
                        $isAssigned = $assigned->has("{$teacher->id}:{$batch->id}");
                        $key        = "cell-{$teacher->id}-{$batch->id}";
                    @endphp

                    @if(!$applicable)
                    {{-- N/A cell --}}
                    <td style="height:72px;width:140px;min-width:140px;border-left:1px solid rgba(245,241,232,0.05);background:rgba(245,241,232,0.02);text-align:center;">
                        <span style="font-size:16px;font-weight:700;color:#6a665f;">—</span>
                    </td>
                    @else
                    {{-- Toggleable cell --}}
                    <td id="{{ $key }}"
                        data-user="{{ $teacher->id }}"
                        data-batch="{{ $batch->id }}"
                        data-assigned="{{ $isAssigned ? '1' : '0' }}"
                        onclick="toggleCell(this)"
                        style="height:72px;width:140px;min-width:140px;border-left:1px solid rgba(245,241,232,0.05);text-align:center;cursor:pointer;
                               {{ $isAssigned ? 'background:rgba(122,149,200,0.08);' : '' }}"
                        onmouseover="if(this.dataset.assigned==='0')this.style.background='rgba(245,241,232,0.04)'"
                        onmouseout="if(this.dataset.assigned==='0')this.style.background='transparent';else this.style.background='rgba(122,149,200,0.08)'">
                        @if($isAssigned)
                        <div style="width:28px;height:28px;border-radius:50%;background:#7a95c8;display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:14px;font-weight:700;color:#14141b;">✓</div>
                        @else
                        <div style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(245,241,232,0.15);margin:0 auto;"></div>
                        @endif
                    </td>
                    @endif
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;">
        <p id="statusLine" style="font-size:12px;color:#6a665f;">
            {{ $assignmentCount }} total assignments · {{ $fullyAssigned }} teachers fully assigned · 0 conflicts
        </p>
        <button onclick="showSaved()"
                style="padding:10px 18px;background:#7a95c8;color:#14141b;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">
            Save Changes
        </button>
    </div>

    @endif
</div>

{{-- Bulk Assign Modal --}}
<div id="bulkModal" style="display:none;position:fixed;inset:0;background:rgba(8,8,10,0.8);z-index:1000;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:16px;width:100%;max-width:480px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid rgba(245,241,232,0.06);">
            <h3 style="font-size:15px;font-weight:600;color:#f5f1e8;margin:0;">Bulk Assign</h3>
            <button onclick="document.getElementById('bulkModal').style.display='none'"
                    style="width:28px;height:28px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:6px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#6a665f;font-size:16px;">×</button>
        </div>
        <form id="bulkForm" method="POST" action="{{ route('admin.assignments.bulk') }}">
            @csrf
            <div style="padding:24px;display:flex;flex-direction:column;gap:16px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;">Teacher</label>
                    <select name="user_id" required style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;cursor:pointer;">
                        <option value="">Select teacher…</option>
                        @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;">Assign to</label>
                    <select name="scope" required style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;cursor:pointer;">
                        <option value="all_applicable">All applicable batches</option>
                        <option value="all">All batches (force)</option>
                    </select>
                </div>
                <p style="font-size:12px;color:#6a665f;">This will assign the teacher to all applicable batches (based on subject match). Existing assignments are kept.</p>
            </div>
            <div style="padding:16px 24px;border-top:1px solid rgba(245,241,232,0.06);display:flex;justify-content:flex-end;gap:10px;">
                <button type="button" onclick="document.getElementById('bulkModal').style.display='none'"
                        style="padding:9px 18px;background:rgba(245,241,232,0.05);border:1px solid rgba(245,241,232,0.08);border-radius:8px;font-size:13px;color:#a8a39c;cursor:pointer;">Cancel</button>
                <button type="submit"
                        style="padding:9px 18px;background:#7a95c8;color:#08080a;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;">Assign</button>
            </div>
        </form>
    </div>
</div>

{{-- Toast --}}
<div id="toast" style="display:none;position:fixed;bottom:24px;right:24px;background:#1a1a24;border:1px solid rgba(127,182,133,0.3);border-radius:8px;padding:12px 18px;font-size:13px;color:#7fb685;z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,0.4);">
    Changes saved.
</div>

<script>
var csrfToken = '{{ csrf_token() }}';
var assignmentCount = {{ $assignmentCount }};

function toggleCell(td) {
    var userId  = td.dataset.user;
    var batchId = td.dataset.batch;
    var was     = td.dataset.assigned === '1';

    // Optimistic UI update
    td.dataset.assigned = was ? '0' : '1';
    td.style.background = was ? 'transparent' : 'rgba(122,149,200,0.08)';
    td.innerHTML = was
        ? '<div style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(245,241,232,0.15);margin:0 auto;"></div>'
        : '<div style="width:28px;height:28px;border-radius:50%;background:#7a95c8;display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:14px;font-weight:700;color:#14141b;">✓</div>';

    assignmentCount += was ? -1 : 1;
    updateStatusLine();

    fetch('{{ route("admin.assignments.toggle") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ user_id: userId, batch_id: batchId })
    })
    .then(r => r.json())
    .then(data => {
        td.dataset.assigned = data.assigned ? '1' : '0';
    })
    .catch(() => {
        // Revert on error
        td.dataset.assigned = was ? '1' : '0';
        td.style.background = was ? 'rgba(122,149,200,0.08)' : 'transparent';
        td.innerHTML = was
            ? '<div style="width:28px;height:28px;border-radius:50%;background:#7a95c8;display:flex;align-items:center;justify-content:center;margin:0 auto;font-size:14px;font-weight:700;color:#14141b;">✓</div>'
            : '<div style="width:28px;height:28px;border-radius:50%;border:1px solid rgba(245,241,232,0.15);margin:0 auto;"></div>';
        assignmentCount += was ? 1 : -1;
        updateStatusLine();
    });
}

function updateStatusLine() {
    var el = document.getElementById('statusLine');
    if (el) el.textContent = assignmentCount + ' total assignments · 0 conflicts';
}

function showSaved() {
    var t = document.getElementById('toast');
    t.style.display = 'block';
    setTimeout(function(){ t.style.display = 'none'; }, 2500);
}

document.getElementById('bulkModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endsection
