@extends('layouts.sub-admin')
@section('title', 'Upload History')
@section('breadcrumb', 'Upload History')

@section('content')
<div style="max-width:1000px;">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:24px;">
        <div>
            <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px;">Upload History</h1>
            <p style="font-size:14px;color:#a8a39c;margin:0;">All OMR uploads for this institute · {{ $uploads->count() }} total</p>
        </div>
        <a href="{{ route('sub-admin.results.upload') }}"
           style="background:#7a95c8;border-radius:6px;padding:11px 16px;font-size:13px;font-weight:600;color:#14141b;text-decoration:none;display:flex;align-items:center;gap:8px;">
            + New Upload
        </a>
    </div>

    @if ($uploads->isEmpty())
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:10px;padding:64px 20px;text-align:center;">
            <p style="font-size:14px;color:#6a665f;margin:0 0 12px;">No uploads yet.</p>
            <a href="{{ route('sub-admin.results.upload') }}" style="font-size:13px;color:#7a95c8;text-decoration:none;">Start first upload →</a>
        </div>
    @else
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;overflow:hidden;">

            {{-- Table header --}}
            <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:200px 1fr 140px 110px 110px 110px;gap:12px;padding:11px 20px;align-items:center;">
                @foreach(['TEST','FILE','UPLOADED BY','DATE','ROWS','STATUS'] as $col)
                    <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1.1px;">{{ $col }}</span>
                @endforeach
            </div>

            @foreach ($uploads as $u)
                @php
                    $statusStyles = match($u->status) {
                        'completed'  => ['#7fb685', 'rgba(127,182,133,0.12)', '✓ Complete'],
                        'validating' => ['#d4a574', 'rgba(212,165,116,0.12)', '⏳ Validating'],
                        'matching'   => ['#d4a574', 'rgba(212,165,116,0.12)', '⏳ Matching'],
                        'failed'     => ['#c87064', 'rgba(200,112,100,0.12)', '✕ Failed'],
                        default      => ['#a8a39c', 'rgba(168,163,156,0.1)',  $u->status],
                    };
                    [$sColor, $sBg, $sLabel] = $statusStyles;
                    $matchRate = $u->total_rows > 0 ? round($u->matched_rows / $u->total_rows * 100) : 0;
                @endphp
                <div style="display:grid;grid-template-columns:200px 1fr 140px 110px 110px 110px;gap:12px;padding:14px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                     onmouseover="this.style.background='rgba(26,26,36,0.5)'" onmouseout="this.style.background=''">

                    {{-- Test --}}
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#f5f1e8;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $u->test_name }}</p>
                        <span style="background:#0f0f14;border-radius:3px;padding:2px 6px;font-size:10px;font-weight:500;color:#a8a39c;font-family:monospace;">{{ $u->test_code }}</span>
                    </div>

                    {{-- File --}}
                    <div style="min-width:0;">
                        <p style="font-size:12px;font-weight:500;color:#f5f1e8;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $u->file_name }}</p>
                        <p style="font-size:11px;color:#6a665f;margin:0;">{{ number_format($u->file_size / 1024, 1) }} KB</p>
                    </div>

                    {{-- Uploaded by --}}
                    <div>
                        <p style="font-size:12px;color:#a8a39c;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $u->uploaded_by }}</p>
                    </div>

                    {{-- Date --}}
                    <div>
                        <p style="font-size:12px;color:#a8a39c;margin:0;">{{ \Carbon\Carbon::parse($u->created_at)->format('d M Y') }}</p>
                        <p style="font-size:10px;color:#6a665f;margin:0;">{{ \Carbon\Carbon::parse($u->created_at)->format('H:i') }}</p>
                    </div>

                    {{-- Rows --}}
                    <div>
                        <p style="font-size:12px;color:#f5f1e8;margin:0 0 2px;">{{ $u->matched_rows }}/{{ $u->total_rows }}</p>
                        <div style="height:3px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                            <div style="height:100%;background:{{ $sColor }};width:{{ $matchRate }}%;"></div>
                        </div>
                        @if ($u->unmatched_rows > 0)
                            <p style="font-size:10px;color:#d4a574;margin:2px 0 0;">{{ $u->unmatched_rows }} unmatched</p>
                        @endif
                    </div>

                    {{-- Status + Action --}}
                    <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-start;">
                        <span style="background:{{ $sBg }};border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:500;color:{{ $sColor }};">{{ $sLabel }}</span>
                        @if ($u->test_status === 'analyzed')
                            <a href="{{ route('sub-admin.tests.results', $u->test_id) }}"
                               style="font-size:11px;color:#7a95c8;text-decoration:none;">View results →</a>
                        @endif
                    </div>

                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
