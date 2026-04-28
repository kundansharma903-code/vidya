@extends('layouts.owner')
@section('title', 'Strategic Alerts')
@section('breadcrumb', 'Strategic Alerts')

@section('content')
<div style="max-width:900px;">
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Strategic Alerts</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Auto-generated from live data · {{ count($alerts) }} active alerts</p>
    </div>

    @php
        $counts = ['critical' => 0, 'warning' => 0, 'success' => 0];
        foreach($alerts as $a) $counts[$a['level']] = ($counts[$a['level']] ?? 0) + 1;
    @endphp
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:20px;">
        <div style="background:rgba(200,112,100,0.08);border:1px solid rgba(200,112,100,0.2);border-radius:8px;padding:14px 16px;text-align:center;">
            <p style="font-size:28px;font-weight:800;color:#c87064;margin:0;">{{ $counts['critical'] }}</p>
            <p style="font-size:10px;color:#c87064;text-transform:uppercase;letter-spacing:0.9px;margin:2px 0 0;">Critical</p>
        </div>
        <div style="background:rgba(212,165,116,0.08);border:1px solid rgba(212,165,116,0.2);border-radius:8px;padding:14px 16px;text-align:center;">
            <p style="font-size:28px;font-weight:800;color:#d4a574;margin:0;">{{ $counts['warning'] }}</p>
            <p style="font-size:10px;color:#d4a574;text-transform:uppercase;letter-spacing:0.9px;margin:2px 0 0;">Warning</p>
        </div>
        <div style="background:rgba(127,182,133,0.08);border:1px solid rgba(127,182,133,0.2);border-radius:8px;padding:14px 16px;text-align:center;">
            <p style="font-size:28px;font-weight:800;color:#7fb685;margin:0;">{{ $counts['success'] }}</p>
            <p style="font-size:10px;color:#7fb685;text-transform:uppercase;letter-spacing:0.9px;margin:2px 0 0;">All Clear</p>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach($alerts as $alert)
        @php
            $cfg = [
                'critical' => ['#c87064', 'rgba(200,112,100,0.08)', 'rgba(200,112,100,0.2)'],
                'warning'  => ['#d4a574', 'rgba(212,165,116,0.06)', 'rgba(212,165,116,0.15)'],
                'success'  => ['#7fb685', 'rgba(127,182,133,0.06)', 'rgba(127,182,133,0.15)'],
            ][$alert['level']] ?? ['#a8a39c','rgba(168,163,156,0.06)','rgba(168,163,156,0.15)'];
        @endphp
        <div style="background:{{ $cfg[1] }};border:1px solid {{ $cfg[2] }};border-radius:8px;padding:16px 20px;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
            <div style="flex:1;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;">
                    <span style="font-size:9px;font-weight:700;color:{{ $cfg[0] }};text-transform:uppercase;letter-spacing:1px;background:{{ $cfg[0] }}20;border-radius:3px;padding:2px 6px;">{{ $alert['level'] }}</span>
                    <span style="font-size:9px;font-weight:600;color:#6a665f;text-transform:uppercase;letter-spacing:0.9px;">{{ $alert['category'] }}</span>
                </div>
                <p style="font-size:13px;color:#d4cfc8;margin:0;line-height:1.6;">{{ $alert['msg'] }}</p>
            </div>
            @if(!empty($alert['action']))
            <a href="{{ $alert['action'] }}" style="font-size:11px;font-weight:600;color:#a392c8;text-decoration:none;background:rgba(163,146,200,0.1);border-radius:5px;padding:6px 12px;white-space:nowrap;flex-shrink:0;border:1px solid rgba(163,146,200,0.2);">
                View →
            </a>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endsection
