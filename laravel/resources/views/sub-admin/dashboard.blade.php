@extends('layouts.sub-admin')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@php
$routeExists = function(string $name): bool {
    try { route($name); return true; } catch (\Exception $e) { return false; }
};
@endphp

@section('content')

    {{-- Greeting --}}
    <div style="margin-bottom:24px;">
        <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px;">{{ $greeting }}, {{ $firstName }}</h1>
        <p style="font-size:14px;color:#a8a39c;margin:0;">
            @if ($pendingTests > 0)
                {{ $pendingTests }} {{ Str::plural('test', $pendingTests) }} ready for upload
                @if ($processingCount > 0) · {{ $processingCount }} processing in background @endif
            @else
                All caught up — no pending uploads
            @endif
        </p>
    </div>

    {{-- CTA Banner --}}
    <div style="background:rgba(122,149,200,0.1);border:1px solid rgba(122,149,200,0.3);border-radius:10px;padding:28px 32px;display:flex;align-items:center;gap:20px;margin-bottom:24px;">
        <div style="width:64px;height:64px;background:rgba(122,149,200,0.18);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:30px;flex-shrink:0;">
            📤
        </div>
        <div style="flex:1;">
            <p style="font-size:18px;font-weight:600;color:#f5f1e8;margin:0 0 4px;">Upload OMR Results</p>
            <p style="font-size:13px;color:#a8a39c;margin:0;">Select test → Upload Excel from OMR scanner → Validate → Process</p>
        </div>
        <div style="display:flex;gap:10px;align-items:center;flex-shrink:0;">
            <a href="{{ $routeExists('sub-admin.tests.create') ? route('sub-admin.tests.create') : '#' }}"
               style="background:#14141b;border:1px solid rgba(245,241,232,0.15);border-radius:8px;padding:12px 20px;font-size:14px;font-weight:600;color:#a8a39c;text-decoration:none;display:flex;align-items:center;gap:6px;">
                + Create Test
            </a>
            <a href="{{ $routeExists('sub-admin.results.upload') ? route('sub-admin.results.upload') : '#' }}"
               style="background:#7a95c8;border-radius:8px;padding:14px 22px;font-size:14px;font-weight:600;color:#14141b;text-decoration:none;display:flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(122,149,200,0.3);">
                <span style="font-weight:700;">+</span> Start Upload <span>→</span>
            </a>
        </div>
    </div>

    {{-- KPI Row --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">

        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 8px;">Uploads Today</p>
            <p style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 8px;">{{ $uploadsToday }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">This session</p>
        </div>

        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 8px;">Pending Tests</p>
            <p style="font-size:28px;font-weight:700;color:#d4a574;letter-spacing:-0.56px;margin:0 0 8px;">{{ $pendingTests }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Ready for upload</p>
        </div>

        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 8px;">Processed</p>
            <p style="font-size:28px;font-weight:700;color:#7fb685;letter-spacing:-0.56px;margin:0 0 8px;">{{ $processedThisMonth }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">This month</p>
        </div>

        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:20px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.88px;text-transform:uppercase;margin:0 0 8px;">Error Rate</p>
            <p style="font-size:28px;font-weight:700;color:#c87064;letter-spacing:-0.56px;margin:0 0 8px;">{{ $errorRate }}%</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Mismatched rolls</p>
        </div>

    </div>

    {{-- Tests Ready for Upload --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;margin-bottom:24px;">

        <div style="padding:16px 20px;border-bottom:1px solid rgba(245,241,232,0.06);display:flex;align-items:center;justify-content:space-between;">
            <div>
                <p style="font-size:15px;font-weight:600;color:#f5f1e8;margin:0 0 2px;">Tests Ready for Upload</p>
                <p style="font-size:11px;color:#6a665f;margin:0;">Tests created, awaiting OMR scan upload</p>
            </div>
            <a href="{{ $routeExists('sub-admin.tests.index') ? route('sub-admin.tests.index') : '#' }}"
               style="font-size:12px;color:#7a95c8;text-decoration:none;font-weight:500;">View all →</a>
        </div>

        @forelse ($pendingTestRows as $test)
            @php
                $dateLabel = match(true) {
                    \Carbon\Carbon::parse($test->test_date)->isToday()     => 'Conducted today',
                    \Carbon\Carbon::parse($test->test_date)->isYesterday() => 'Yesterday',
                    default => \Carbon\Carbon::parse($test->test_date)->diffForHumans(),
                };
            @endphp
            <div style="display:flex;align-items:center;gap:16px;padding:0 20px;height:64px;border-bottom:1px solid rgba(245,241,232,0.05);">

                <div style="background:#0f0f14;border-radius:4px;padding:5px 10px;flex-shrink:0;">
                    <span style="font-size:11px;font-weight:500;color:#a8a39c;">{{ $test->test_code }}</span>
                </div>

                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0 0 3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $test->name }}</p>
                    <p style="font-size:11px;color:#6a665f;margin:0;">{{ ucfirst($test->status) }}</p>
                </div>

                <span style="font-size:12px;color:#a8a39c;flex-shrink:0;">{{ $dateLabel }}</span>

                <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                    <span style="font-size:12px;">👥</span>
                    <span style="font-size:12px;font-weight:500;color:#a8a39c;">{{ $test->student_count }}</span>
                </div>

                <a href="{{ $routeExists('sub-admin.results.upload') ? route('sub-admin.results.upload') : '#' }}"
                   style="background:#7a95c8;border-radius:6px;padding:8px 14px;font-size:12px;font-weight:600;color:#14141b;text-decoration:none;display:flex;align-items:center;gap:6px;flex-shrink:0;">
                    ↑ Upload OMR
                </a>

            </div>
        @empty
            <div style="padding:32px 20px;text-align:center;">
                <p style="font-size:13px;color:#6a665f;margin:0;">No tests pending upload. <a href="{{ route('sub-admin.tests.create') }}" style="color:#7a95c8;text-decoration:none;">Create a test →</a></p>
            </div>
        @endforelse

    </div>

    {{-- Recent Uploads --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;">

        <div style="padding:14px 20px;border-bottom:1px solid rgba(245,241,232,0.06);display:flex;align-items:center;justify-content:space-between;">
            <p style="font-size:14px;font-weight:600;color:#f5f1e8;margin:0;">Recent Uploads</p>
            <a href="{{ $routeExists('sub-admin.results.history') ? route('sub-admin.results.history') : '#' }}"
               style="font-size:12px;color:#7a95c8;text-decoration:none;font-weight:500;">View history →</a>
        </div>

        @forelse ($recentUploads as $upload)
            @php
                $statusConfig = match($upload->upload_status) {
                    'completed'  => ['label' => 'Complete',   'color' => '#7fb685', 'bg' => 'rgba(127,182,133,0.12)'],
                    'failed'     => ['label' => 'Failed',     'color' => '#c87064', 'bg' => 'rgba(200,112,100,0.12)'],
                    'validating',
                    'matching',
                    'uploaded'   => ['label' => 'Processing', 'color' => '#d4a574', 'bg' => 'rgba(212,165,116,0.12)'],
                    default      => ['label' => ucfirst($upload->upload_status), 'color' => '#a8a39c', 'bg' => 'rgba(168,163,156,0.1)'],
                };
                if ($upload->upload_status === 'completed' && $upload->unmatched_rows > 0) {
                    $statusConfig = ['label' => 'Needs Review', 'color' => '#c87064', 'bg' => 'rgba(200,112,100,0.12)'];
                }
                $timeAgo = \Carbon\Carbon::parse($upload->created_at)->diffForHumans();
            @endphp
            <div style="display:flex;align-items:center;gap:16px;padding:0 20px;height:52px;border-bottom:1px solid rgba(245,241,232,0.05);">

                <div style="background:#0f0f14;border-radius:4px;padding:4px 8px;flex-shrink:0;">
                    <span style="font-size:11px;font-weight:500;color:#a8a39c;">{{ $upload->test_code ?? '—' }}</span>
                </div>

                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $upload->test_name ?? 'Unknown Test' }}</p>
                </div>

                <span style="font-size:12px;color:#6a665f;flex-shrink:0;">{{ ucfirst($upload->upload_status) }} · {{ $timeAgo }}</span>

                <div style="background:{{ $statusConfig['bg'] }};border-radius:9999px;padding:4px 10px 4px 8px;display:flex;align-items:center;gap:6px;flex-shrink:0;">
                    <span style="width:6px;height:6px;background:{{ $statusConfig['color'] }};border-radius:50%;display:inline-block;"></span>
                    <span style="font-size:11px;font-weight:500;color:{{ $statusConfig['color'] }};">{{ $statusConfig['label'] }}</span>
                </div>

            </div>
        @empty
            <div style="padding:32px 20px;text-align:center;">
                <p style="font-size:13px;color:#6a665f;margin:0;">No uploads yet.</p>
            </div>
        @endforelse

    </div>

@endsection
