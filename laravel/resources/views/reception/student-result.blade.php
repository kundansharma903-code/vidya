@extends('layouts.reception')
@section('title', $student->name.' · '.$test->test_code)
@section('breadcrumb', $student->name.' — Result')

@push('styles')
<style>
@media print {
    .sidebar, .topbar, .no-print { display: none !important; }
    .main { margin-left: 0 !important; padding-top: 0 !important; }
    body { background: #fff !important; color: #000 !important; }
    .print-card { background: #fff !important; border: 1px solid #ddd !important; color: #000 !important; }
    .print-text { color: #000 !important; }
}
</style>
@endpush

@section('content')
<div style="max-width:1040px;">

    {{-- Back + actions --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;" class="no-print">
        <a href="{{ route('reception.test-results', $test->id) }}" style="font-size:12px;color:#6a665f;text-decoration:none;display:inline-flex;align-items:center;gap:5px;">← Back to {{ $test->test_code }} Results</a>
        <div style="display:flex;gap:8px;">
            <button onclick="window.print()" style="background:rgba(200,112,100,0.1);border:1px solid rgba(200,112,100,0.25);border-radius:6px;padding:7px 14px;color:#c87064;font-size:12px;font-weight:600;cursor:pointer;">
                🖨 Print Report
            </button>
            <button onclick="showToast('WhatsApp feature coming soon')" style="background:rgba(127,182,133,0.1);border:1px solid rgba(127,182,133,0.2);border-radius:6px;padding:7px 14px;color:#7fb685;font-size:12px;font-weight:600;cursor:pointer;">
                📱 Send to WhatsApp
            </button>
        </div>
    </div>

    {{-- Student + test header --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 26px;margin-bottom:16px;" class="print-card">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:48px;height:48px;border-radius:8px;background:rgba(200,112,100,0.12);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:800;color:#c87064;">
                    {{ strtoupper(substr($student->name,0,1)) }}
                </div>
                <div>
                    <h1 style="font-size:20px;font-weight:700;color:#f5f1e8;margin:0 0 3px;" class="print-text">{{ $student->name }}</h1>
                    <p style="font-size:12px;color:#6a665f;margin:0;">Roll: {{ $student->roll_number }} · {{ $batch->name }}</p>
                </div>
            </div>
            <div style="text-align:right;">
                <p style="font-size:12px;color:#6a665f;margin:0 0 3px;">{{ $test->name }}</p>
                <p style="font-size:11px;color:#4a4740;margin:0;">{{ $test->test_code }} · {{ \Carbon\Carbon::parse($test->test_date)->format('d M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Score banner --}}
    @php
        $pct       = $maxMarks > 0 ? (int)round($result->total_marks / $maxMarks * 100) : 0;
        $scoreColor= $pct >= 60 ? '#7fb685' : ($pct >= 35 ? '#d4a574' : '#c87064');
    @endphp
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:16px;">
        @foreach([
            ['Total Score',  $result->total_marks.'/'.$maxMarks, $scoreColor],
            ['Percentage',   $pct.'%',                           $scoreColor],
            ['Rank',         '#'.$result->rank_in_batch.' / '.$totalInTest, '#d4a574'],
            ['Percentile',   round($result->percentile,1).'%',   '#7a95c8'],
            ['Correct / Wrong', $result->total_correct.' / '.$result->total_incorrect, '#f5f1e8'],
        ] as [$lbl,$val,$col])
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:14px 16px;text-align:center;" class="print-card">
            <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;margin:0 0 4px;">{{ $lbl }}</p>
            <p style="font-size:18px;font-weight:800;color:{{ $col }};margin:0;letter-spacing:-0.4px;" class="print-text">{{ $val }}</p>
        </div>
        @endforeach
    </div>

    {{-- Subject breakdown --}}
    @if(!empty($subjectBreakdown))
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 24px;margin-bottom:16px;" class="print-card">
        <h2 style="font-size:13px;font-weight:700;color:#f5f1e8;margin:0 0 14px;" class="print-text">Subject Breakdown</h2>
        <div style="display:grid;grid-template-columns:repeat({{ min(4,count($subjectBreakdown)) }},1fr);gap:10px;">
            @foreach($subjectBreakdown as $sb)
            @php
                $sp = isset($sb['marks_possible']) && $sb['marks_possible'] > 0
                    ? (int)round(($sb['marks_earned'] ?? 0) / $sb['marks_possible'] * 100) : 0;
                $sc = $sp >= 60 ? '#7fb685' : ($sp >= 35 ? '#d4a574' : '#c87064');
            @endphp
            <div style="background:#0f0f14;border-radius:6px;padding:12px 14px;" class="print-card">
                <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;margin:0 0 4px;">{{ $sb['name'] }}</p>
                <p style="font-size:18px;font-weight:800;color:{{ $sc }};margin:0 0 2px;" class="print-text">{{ $sb['marks_earned'] ?? 0 }}/{{ $sb['marks_possible'] ?? 0 }}</p>
                <p style="font-size:10px;color:#6a665f;margin:0;">
                    <span style="color:#7fb685;">{{ $sb['correct'] ?? 0 }}✓</span>
                    <span style="color:#c87064;margin-left:4px;">{{ $sb['incorrect'] ?? 0 }}✗</span>
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Q-by-Q answer grid --}}
    @if($responses->isNotEmpty())
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 24px;" class="print-card">
        <h2 style="font-size:13px;font-weight:700;color:#f5f1e8;margin:0 0 14px;" class="print-text">Question-by-Question Review</h2>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(52px,1fr));gap:6px;">
            @foreach($responses as $resp)
            @php
                $qc = $resp->is_correct ? '#7fb685' : ($resp->submitted_answer ? '#c87064' : '#4a4740');
                $bg = $resp->is_correct ? 'rgba(127,182,133,0.12)' : ($resp->submitted_answer ? 'rgba(200,112,100,0.1)' : 'rgba(245,241,232,0.03)');
                $border = $resp->is_correct ? 'rgba(127,182,133,0.3)' : ($resp->submitted_answer ? 'rgba(200,112,100,0.25)' : 'rgba(245,241,232,0.06)');
            @endphp
            <div style="background:{{ $bg }};border:1px solid {{ $border }};border-radius:5px;padding:6px 4px;text-align:center;" title="Q{{ $resp->question_number }}: {{ $resp->topic_code }}">
                <p style="font-size:9px;color:#4a4740;margin:0 0 2px;">Q{{ $resp->question_number }}</p>
                <p style="font-size:13px;font-weight:700;color:{{ $qc }};margin:0;">{{ $resp->submitted_answer ?: '—' }}</p>
                @if(!$resp->is_correct && $resp->submitted_answer)
                <p style="font-size:8px;color:#6a665f;margin:1px 0 0;">Ans: {{ $resp->correct_answer }}</p>
                @endif
            </div>
            @endforeach
        </div>
        <div style="display:flex;gap:14px;margin-top:14px;">
            <div style="display:flex;align-items:center;gap:5px;"><div style="width:12px;height:12px;background:rgba(127,182,133,0.3);border:1px solid rgba(127,182,133,0.5);border-radius:3px;"></div><span style="font-size:10px;color:#6a665f;">Correct</span></div>
            <div style="display:flex;align-items:center;gap:5px;"><div style="width:12px;height:12px;background:rgba(200,112,100,0.2);border:1px solid rgba(200,112,100,0.4);border-radius:3px;"></div><span style="font-size:10px;color:#6a665f;">Wrong</span></div>
            <div style="display:flex;align-items:center;gap:5px;"><div style="width:12px;height:12px;background:rgba(245,241,232,0.04);border:1px solid rgba(245,241,232,0.08);border-radius:3px;"></div><span style="font-size:10px;color:#6a665f;">Unattempted</span></div>
        </div>
    </div>
    @endif
</div>

<div id="toast" style="display:none;position:fixed;bottom:24px;right:24px;background:#14141b;border:1px solid rgba(200,112,100,0.3);border-radius:8px;padding:14px 20px;z-index:9999;color:#f5f1e8;font-size:13px;">
    <span id="toastMsg"></span>
</div>
@push('scripts')
<script>
function showToast(msg) {
    document.getElementById('toastMsg').textContent = msg;
    var t = document.getElementById('toast');
    t.style.display = 'block';
    setTimeout(function(){ t.style.display = 'none'; }, 3000);
}
</script>
@endpush
@endsection
