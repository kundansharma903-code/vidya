@extends('layouts.academic-head')
@section('title', 'Subject Performance')
@section('breadcrumb', 'Subject Performance')

@section('content')
<div style="max-width:1060px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Subject Performance</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Institute-wide performance breakdown by subject</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        @foreach ($subjectData as $sp)
            @php
                $mc = $sp['avg'] >= 70 ? '#7fb685' : ($sp['avg'] >= 40 ? '#d4a574' : '#c87064');
                $circ3 = 339.3;
                $off3  = $circ3 - ($sp['avg'] / 100 * $circ3);
            @endphp
            <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px 24px;">

                {{-- Header --}}
                <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px;">
                    <div style="width:46px;height:46px;border-radius:8px;background:rgba({{ implode(',',sscanf($sp['color'],'#%02x%02x%02x')) }},0.15);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:{{ $sp['color'] }};">
                        {{ $sp['code'] }}
                    </div>
                    <div>
                        <h2 style="font-size:17px;font-weight:700;color:#f5f1e8;margin:0;">{{ $sp['name'] }}</h2>
                        <p style="font-size:11px;color:#6a665f;margin:0;">{{ $sp['students'] }} students · {{ $sp['covered'] }}/{{ $sp['total'] }} topics covered</p>
                    </div>
                </div>

                {{-- Stats grid --}}
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px;">
                    <div style="background:#0f0f14;border-radius:6px;padding:10px 12px;">
                        <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.88px;margin:0 0 3px;">Avg Mastery</p>
                        <p style="font-size:20px;font-weight:700;color:{{ $mc }};margin:0;letter-spacing:-0.4px;">{{ $sp['avg'] }}%</p>
                    </div>
                    <div style="background:#0f0f14;border-radius:6px;padding:10px 12px;">
                        <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.88px;margin:0 0 3px;">Weak Topics</p>
                        <p style="font-size:20px;font-weight:700;color:#c87064;margin:0;letter-spacing:-0.4px;">{{ $sp['weakCount'] }}</p>
                    </div>
                    <div style="background:#0f0f14;border-radius:6px;padding:10px 12px;">
                        <p style="font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:0.88px;margin:0 0 3px;">Questions</p>
                        <p style="font-size:20px;font-weight:700;color:#a8a39c;margin:0;letter-spacing:-0.4px;">{{ $sp['attempted'] ?: '—' }}</p>
                    </div>
                </div>

                {{-- Mastery bar --}}
                <div style="margin-bottom:8px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:11px;color:#6a665f;">Class Average Mastery</span>
                        <span style="font-size:12px;font-weight:600;color:{{ $mc }};">{{ $sp['avg'] }}%</span>
                    </div>
                    <div style="height:7px;background:rgba(245,241,232,0.06);border-radius:4px;overflow:hidden;">
                        <div style="height:100%;background:{{ $mc }};width:{{ $sp['avg'] }}%;"></div>
                    </div>
                </div>

                {{-- Coverage bar --}}
                @php $covPct = $sp['total'] > 0 ? round($sp['covered']/$sp['total']*100) : 0; @endphp
                <div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:5px;">
                        <span style="font-size:11px;color:#6a665f;">Curriculum Coverage</span>
                        <span style="font-size:12px;font-weight:600;color:{{ $sp['color'] }};">{{ $covPct }}%</span>
                    </div>
                    <div style="height:7px;background:rgba(245,241,232,0.06);border-radius:4px;overflow:hidden;">
                        <div style="height:100%;background:{{ $sp['color'] }};width:{{ $covPct }}%;opacity:0.7;"></div>
                    </div>
                </div>

                {{-- Compare teachers link --}}
                <div style="margin-top:14px;padding-top:12px;border-top:1px solid rgba(245,241,232,0.06);display:flex;align-items:center;justify-content:flex-end;">
                    <a href="{{ route('academic-head.subject-comparison', $sp['id']) }}"
                       style="font-size:12px;font-weight:600;color:#7a95c8;text-decoration:none;background:rgba(122,149,200,0.1);border-radius:4px;padding:5px 10px;">
                        Teacher Comparison →
                    </a>
                </div>

                @if ($sp['total'] === 0)
                    <div style="margin-top:12px;background:rgba(212,165,116,0.08);border-radius:6px;padding:8px 10px;">
                        <p style="font-size:11px;color:#d4a574;margin:0;">⚠ No topics added to curriculum yet</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
