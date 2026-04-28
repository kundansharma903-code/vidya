@extends('layouts.owner')
@section('title', 'Staff Decisions')
@section('breadcrumb', 'Staff Decisions')

@section('content')
<div style="max-width:900px;">
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Staff Decisions</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">AI-generated pending decisions based on live performance data</p>
    </div>

    @forelse($decisions as $d)
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 24px;margin-bottom:12px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:40px;height:40px;border-radius:8px;background:rgba(163,146,200,0.12);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:#a392c8;">
                    {{ strtoupper(substr($d['teacher']->name,0,1)) }}{{ strtoupper(substr(explode(' ',$d['teacher']->name)[1]??'X',0,1)) }}
                </div>
                <div>
                    <h2 style="font-size:15px;font-weight:700;color:#f5f1e8;margin:0;">{{ $d['teacher']->name }}</h2>
                    <p style="font-size:11px;color:#6a665f;margin:0;">{{ $d['subjectName'] }} · {{ $d['students'] }} students · Class avg {{ $d['classAvg'] }}% · ROI {{ $d['roi'] }}x</p>
                </div>
            </div>
            <a href="{{ route('owner.teacher-deep-dive', $d['teacher']->id) }}" style="font-size:11px;color:#a392c8;text-decoration:none;">Full Profile →</a>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:8px;">
            @foreach($d['pending'] as $p)
            <div style="background:{{ $p['color'] }}12;border:1px solid {{ $p['color'] }}30;border-radius:8px;padding:12px 16px;flex:1;min-width:160px;">
                <p style="font-size:12px;font-weight:700;color:{{ $p['color'] }};margin:0 0 4px;">{{ $p['label'] }}</p>
                <p style="font-size:11px;color:#a8a39c;margin:0 0 10px;line-height:1.4;">{{ $p['reason'] }}</p>
                <div style="display:flex;gap:6px;">
                    <button onclick="handleDecision('{{ $p['type'] }}','{{ $d['teacher']->name }}')" style="flex:1;padding:5px 0;font-size:10px;font-weight:700;color:{{ $p['color'] }};background:{{ $p['color'] }}20;border:1px solid {{ $p['color'] }}40;border-radius:4px;cursor:pointer;">
                        Approve
                    </button>
                    <button onclick="handleDecision('dismiss','{{ $d['teacher']->name }}')" style="flex:1;padding:5px 0;font-size:10px;font-weight:600;color:#6a665f;background:transparent;border:1px solid rgba(245,241,232,0.08);border-radius:4px;cursor:pointer;">
                        Dismiss
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:48px;text-align:center;">
        <p style="font-size:20px;margin:0 0 8px;">✓</p>
        <p style="font-size:14px;font-weight:600;color:#7fb685;margin:0 0 4px;">No pending decisions</p>
        <p style="font-size:12px;color:#6a665f;margin:0;">All teachers are within acceptable performance thresholds.</p>
    </div>
    @endforelse
</div>

<div id="decisionToast" style="display:none;position:fixed;bottom:24px;right:24px;background:#14141b;border:1px solid rgba(163,146,200,0.3);border-radius:8px;padding:14px 20px;z-index:9999;color:#f5f1e8;font-size:13px;box-shadow:0 8px 24px rgba(0,0,0,0.4);">
    <strong id="toastMsg"></strong>
</div>
@push('scripts')
<script>
function handleDecision(type, name) {
    var msgs = {
        promote:  'Promotion initiated for ' + name,
        raise:    'Raise request logged for ' + name,
        training: 'Training enrolled for ' + name,
        warning:  'Performance warning issued to ' + name,
        dismiss:  'Decision dismissed for ' + name,
    };
    document.getElementById('toastMsg').textContent = msgs[type] || 'Action logged';
    var t = document.getElementById('decisionToast');
    t.style.display = 'block';
    setTimeout(function(){ t.style.display = 'none'; }, 3000);
}
</script>
@endpush
@endsection
