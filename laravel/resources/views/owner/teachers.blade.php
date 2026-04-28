@extends('layouts.owner')
@section('title', 'Teacher Performance')
@section('breadcrumb', 'Teacher Performance')

@section('content')
<div style="max-width:1060px;">
    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Teacher Performance</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">ROI-ranked teacher analysis — click row to deep dive</p>
    </div>

    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid rgba(245,241,232,0.08);">
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 22px;font-weight:700;">#</th>
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 10px;font-weight:700;">Teacher</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 10px;font-weight:700;">Subject</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 10px;font-weight:700;">Students</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 10px;font-weight:700;">Class Avg</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 10px;font-weight:700;">Annual Salary</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:14px 10px;font-weight:700;">Annual Revenue</th>
                    <th style="text-align:center;font-size:9px;color:#a392c8;text-transform:uppercase;letter-spacing:1px;padding:14px 22px;font-weight:700;">ROI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teacherData as $i => $td)
                @php
                    $mc      = $td['classAvg'] >= 60 ? '#7fb685' : ($td['classAvg'] >= 40 ? '#d4a574' : '#c87064');
                    $roiColor= $td['roi'] >= 5 ? '#7fb685' : ($td['roi'] >= 2.5 ? '#d4a574' : '#c87064');
                    $rankColors = ['#d4a574','#a8a39c','#c87064'];
                @endphp
                <tr onclick="window.location='{{ route('owner.teacher-deep-dive', $td['teacher']->id) }}'"
                    style="border-bottom:1px solid rgba(245,241,232,0.04);cursor:pointer;transition:background 0.15s;"
                    onmouseover="this.style.background='rgba(245,241,232,0.03)'" onmouseout="this.style.background='transparent'">
                    <td style="padding:14px 22px;font-size:13px;font-weight:700;color:{{ $rankColors[$i] ?? '#6a665f' }};">{{ $i + 1 }}</td>
                    <td style="padding:14px 10px;">
                        <div style="display:flex;align-items:center;gap:9px;">
                            <div style="width:32px;height:32px;border-radius:6px;background:rgba(163,146,200,0.12);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#a392c8;flex-shrink:0;">
                                {{ strtoupper(substr($td['teacher']->name,0,1)) }}{{ strtoupper(substr(explode(' ',$td['teacher']->name)[1]??'X',0,1)) }}
                            </div>
                            <div>
                                <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">{{ $td['teacher']->name }}</p>
                                <p style="font-size:10px;color:#6a665f;margin:0;">{{ $td['weakCount'] }} weak topics</p>
                            </div>
                        </div>
                    </td>
                    <td style="text-align:center;padding:14px 10px;font-size:12px;color:#a8a39c;">{{ $td['subjectName'] }}</td>
                    <td style="text-align:center;padding:14px 10px;font-size:13px;color:#d4cfc8;">{{ $td['students'] }}</td>
                    <td style="text-align:center;padding:14px 10px;font-size:14px;font-weight:700;color:{{ $mc }};">{{ $td['classAvg'] }}%</td>
                    <td style="text-align:center;padding:14px 10px;font-size:12px;color:#d4a574;">₹{{ number_format($td['annualSalary']) }}</td>
                    <td style="text-align:center;padding:14px 10px;font-size:12px;color:#7fb685;">₹{{ number_format($td['annualRevenue']) }}</td>
                    <td style="text-align:center;padding:14px 22px;font-size:18px;font-weight:800;color:{{ $roiColor }};letter-spacing:-0.4px;">{{ $td['roi'] }}x</td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:40px;color:#6a665f;font-size:14px;">No teachers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
