@php
$steps = [
    1 => 'Select Test',
    2 => 'Upload',
    3 => 'Validate',
    4 => 'Process',
];
@endphp
<div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:20px 24px;display:flex;align-items:center;gap:0;margin-bottom:24px;">
    @foreach ($steps as $num => $label)
        @php $active = $num === $currentStep; $done = $num < $currentStep; @endphp

        <div style="display:flex;align-items:center;gap:10px;flex-shrink:0;">
            {{-- Circle --}}
            <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;
                        background:{{ $done ? '#7fb685' : ($active ? '#7a95c8' : '#1a1a24') }};
                        border:{{ (!$done && !$active) ? '1px solid rgba(245,241,232,0.15)' : 'none' }};">
                @if ($done)
                    <span style="font-size:12px;font-weight:700;color:#14141b;">✓</span>
                @else
                    <span style="font-size:13px;font-weight:600;color:{{ $active ? '#14141b' : '#6a665f' }};">{{ $num }}</span>
                @endif
            </div>
            {{-- Label --}}
            <div>
                <p style="font-size:9px;font-weight:500;color:#6a665f;letter-spacing:1.08px;text-transform:uppercase;margin:0;">STEP {{ $num }}</p>
                <p style="font-size:13px;font-weight:{{ $active ? '600' : '500' }};color:{{ $active ? '#f5f1e8' : '#6a665f' }};margin:0;">{{ $label }}</p>
            </div>
        </div>

        @if (!$loop->last)
            <div style="flex:1;height:1px;background:rgba(245,241,232,0.1);margin:0 12px;min-width:20px;"></div>
        @endif
    @endforeach
</div>
