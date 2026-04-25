@extends('layouts.teacher')
@section('title', 'Class Heatmap')
@section('breadcrumb', 'Class Heatmap')

@section('content')
<div style="max-width:1040px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Class Heatmap</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Topic × Batch mastery matrix · {{ $subject?->name }}</p>
    </div>

    {{-- Legend --}}
    <div style="display:flex;align-items:center;gap:16px;margin-bottom:18px;">
        <span style="font-size:11px;color:#6a665f;">Mastery:</span>
        @foreach([['≥70%','#7fb685','rgba(127,182,133,0.15)'],['40–69%','#d4a574','rgba(212,165,116,0.15)'],['<40%','#c87064','rgba(200,112,100,0.15)'],['No data','#6a665f','rgba(245,241,232,0.04)']] as [$label, $color, $bg])
            <div style="display:flex;align-items:center;gap:6px;">
                <div style="width:14px;height:14px;border-radius:3px;background:{{ $bg }};border:1px solid {{ $color }}20;"></div>
                <span style="font-size:11px;color:#a8a39c;">{{ $label }}</span>
            </div>
        @endforeach
    </div>

    @if ($topics->isEmpty())
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:48px;text-align:center;">
            <p style="font-size:14px;color:#6a665f;margin:0;">No topic data available yet. Upload OMR results first.</p>
        </div>
    @else
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">

            {{-- Table header --}}
            <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:180px {{ str_repeat('1fr ', $batches->count()) }};gap:0;padding:10px 16px;align-items:center;">
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">TOPIC</span>
                @foreach ($batches as $b)
                    <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-align:center;">{{ strtoupper($b->name) }}</span>
                @endforeach
            </div>

            {{-- Rows --}}
            @foreach ($topics as $topic)
                <div style="display:grid;grid-template-columns:180px {{ str_repeat('1fr ', $batches->count()) }};gap:0;border-bottom:1px solid rgba(245,241,232,0.04);align-items:center;"
                     onmouseover="this.style.background='rgba(26,26,36,0.4)'" onmouseout="this.style.background=''">

                    <div style="padding:10px 16px;">
                        <a href="{{ route('teacher.topics.detail', $topic->code) }}" style="text-decoration:none;">
                            <p style="font-size:12px;color:#f5f1e8;margin:0 0 2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $topic->name }}</p>
                            <span style="font-size:10px;color:#6a665f;font-family:monospace;">{{ $topic->code }}</span>
                        </a>
                    </div>

                    @foreach ($batches as $b)
                        @php
                            $val = $heatmap[$topic->id][$b->id] ?? null;
                            if ($val === null) {
                                $bg = 'rgba(245,241,232,0.03)'; $color = '#6a665f'; $border = 'transparent';
                            } elseif ($val >= 70) {
                                $bg = 'rgba(127,182,133,0.12)'; $color = '#7fb685'; $border = 'rgba(127,182,133,0.2)';
                            } elseif ($val >= 40) {
                                $bg = 'rgba(212,165,116,0.12)'; $color = '#d4a574'; $border = 'rgba(212,165,116,0.2)';
                            } else {
                                $bg = 'rgba(200,112,100,0.12)'; $color = '#c87064'; $border = 'rgba(200,112,100,0.2)';
                            }
                        @endphp
                        <div style="padding:8px;display:flex;justify-content:center;">
                            <span style="background:{{ $bg }};border:1px solid {{ $border }};border-radius:6px;padding:4px 10px;font-size:12px;font-weight:600;color:{{ $color }};min-width:48px;text-align:center;display:inline-block;">
                                {{ $val !== null ? $val.'%' : '–' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <p style="font-size:11px;color:#6a665f;margin-top:10px;">Click a topic name to see individual student breakdown.</p>
    @endif
</div>
@endsection
