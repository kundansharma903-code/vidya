@extends('layouts.reception')
@section('title', 'Recent Walk-ins')
@section('breadcrumb', 'Recent Walk-ins')

@section('content')
<div style="max-width:900px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Recent Walk-ins</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Your lookup history</p>
    </div>

    {{-- Range filter --}}
    <div style="display:flex;gap:6px;margin-bottom:20px;">
        @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month'] as $val => $label)
        <a href="{{ route('reception.walk-ins', ['range' => $val]) }}"
           style="font-size:12px;font-weight:600;padding:6px 16px;border-radius:6px;text-decoration:none;border:1px solid {{ $range === $val ? 'rgba(200,112,100,0.4)' : 'rgba(245,241,232,0.08)' }};background:{{ $range === $val ? 'rgba(200,112,100,0.12)' : 'transparent' }};color:{{ $range === $val ? '#c87064' : '#6a665f' }};">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid rgba(245,241,232,0.08);">
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 22px;font-weight:700;">Time</th>
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Student</th>
                    <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Test Viewed</th>
                    <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 10px;font-weight:700;">Type</th>
                    <th style="text-align:right;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:13px 22px;font-weight:700;">Staff</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr style="border-bottom:1px solid rgba(245,241,232,0.04);">
                    <td style="padding:12px 22px;font-size:11px;color:#6a665f;white-space:nowrap;">
                        {{ \Carbon\Carbon::parse($log->viewed_at)->format('d M, H:i') }}
                    </td>
                    <td style="padding:12px 10px;">
                        <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0;">{{ $log->student_name }}</p>
                        <p style="font-size:10px;color:#6a665f;margin:0;">{{ $log->roll_number }}</p>
                    </td>
                    <td style="padding:12px 10px;">
                        @if($log->test_name)
                        <a href="{{ route('reception.student-result', [$log->student_id, $log->test_id]) }}"
                           style="font-size:12px;color:#c87064;text-decoration:none;font-weight:500;">
                            {{ $log->test_name }}
                        </a>
                        <p style="font-size:10px;color:#6a665f;margin:2px 0 0;">{{ $log->test_code }}</p>
                        @else
                        <span style="font-size:12px;color:#4a4740;">General inquiry</span>
                        @endif
                    </td>
                    <td style="text-align:center;padding:12px 10px;">
                        <span style="font-size:10px;color:#d4a574;background:rgba(212,165,116,0.1);border-radius:4px;padding:3px 8px;">
                            {{ $log->query_type === 'result_lookup' ? 'Result' : 'Inquiry' }}
                        </span>
                    </td>
                    <td style="text-align:right;padding:12px 22px;font-size:12px;color:#6a665f;">{{ $log->staff_name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:48px;color:#6a665f;font-size:14px;">
                        No walk-ins recorded {{ $range === 'today' ? 'today' : 'in this period' }}.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div style="margin-top:16px;display:flex;justify-content:center;">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
