@extends('layouts.sub-admin')
@section('title', 'Upload Results — Select Test')
@section('breadcrumb', 'Select Test')

@section('content')
<div style="max-width:960px;">

    {{-- Page heading --}}
    <div style="margin-bottom:24px;">
        <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px;">Upload OMR Responses</h1>
        <p style="font-size:14px;color:#a8a39c;margin:0;">Step 1 of 4 — Select the test for which you're uploading OMR responses</p>
    </div>

    {{-- Stepper --}}
    @include('sub-admin.results._stepper', ['currentStep' => 1])

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('sub-admin.results.upload') }}" style="display:flex;gap:12px;margin-bottom:16px;align-items:center;">
        <div style="flex:1;position:relative;">
            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);font-size:14px;color:#6a665f;">⌕</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by test code, name, or course…"
                   style="width:100%;box-sizing:border-box;background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px 11px 36px;font-size:13px;color:#f5f1e8;outline:none;"
                   onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.1)'">
        </div>
        <select name="course_id" onchange="this.form.submit()"
                style="background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#a8a39c;outline:none;cursor:pointer;appearance:none;min-width:150px;">
            <option value="">All Courses</option>
            @foreach ($courses as $c)
                <option value="{{ $c->id }}" {{ $courseId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
            @endforeach
        </select>
        <select name="test_type" onchange="this.form.submit()"
                style="background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:11px 14px;font-size:13px;color:#a8a39c;outline:none;cursor:pointer;appearance:none;min-width:150px;">
            <option value="">All Test Types</option>
            @foreach(['mock'=>'Mock','dpt'=>'DPT','weekly'=>'Weekly','flt'=>'Full Length','chapter'=>'Chapter','revision'=>'Revision'] as $val => $label)
                <option value="{{ $val }}" {{ $testType === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @if ($search || $courseId || $testType)
            <a href="{{ route('sub-admin.results.upload') }}" style="font-size:12px;color:#6a665f;text-decoration:none;padding:10px 4px;white-space:nowrap;">Clear ✕</a>
        @endif
    </form>

    {{-- Info banner --}}
    <div style="background:rgba(122,149,200,0.1);border:1px solid rgba(122,149,200,0.2);border-radius:6px;padding:12px 16px;display:flex;align-items:center;gap:10px;margin-bottom:20px;">
        <span style="color:#7a95c8;font-size:16px;font-weight:700;flex-shrink:0;">ℹ</span>
        <p style="font-size:12px;color:#a8a39c;margin:0;line-height:1.6;">
            Only tests that have been scheduled or conducted and are awaiting OMR upload are shown below. Tests already processed will appear in Upload History.
        </p>
    </div>

    {{-- Test selection form --}}
    <form method="POST" action="{{ route('sub-admin.results.select') }}" id="selectForm">
        @csrf

        {{-- Test cards --}}
        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:24px;">
            @forelse ($tests as $test)
                @php
                    $dateLabel = \Carbon\Carbon::parse($test->test_date)->format('d M Y');
                    $isToday   = \Carbon\Carbon::parse($test->test_date)->isToday();
                @endphp
                <label for="test_{{ $test->id }}" style="cursor:pointer;display:block;">
                    <input type="radio" name="test_id" id="test_{{ $test->id }}" value="{{ $test->id }}"
                           style="display:none;" onchange="highlightCard(this)">
                    <div class="test-card" id="card_{{ $test->id }}"
                         style="background:#14141b;border:1px solid rgba(122,149,200,0.08);border-radius:10px;padding:18px 20px;display:flex;align-items:center;gap:20px;transition:border-color 0.15s,background 0.15s;">

                        {{-- Radio indicator --}}
                        <div id="radio_{{ $test->id }}"
                             style="width:20px;height:20px;border-radius:10px;border:1.5px solid rgba(245,241,232,0.2);background:#0f0f14;flex-shrink:0;display:flex;align-items:center;justify-content:center;">
                        </div>

                        {{-- Test info --}}
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;flex-wrap:wrap;">
                                <span style="background:#0f0f14;border-radius:4px;padding:4px 8px;font-size:11px;font-weight:500;color:#a8a39c;">{{ $test->test_code }}</span>
                                <span style="font-size:15px;font-weight:600;color:#f5f1e8;">{{ $test->name }}</span>
                                @if ($test->course_name)
                                    <span style="background:rgba(122,149,200,0.12);border-radius:9999px;padding:3px 10px;font-size:11px;font-weight:500;color:#7a95c8;">{{ $test->course_name }}</span>
                                @endif
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                <span style="font-size:12px;color:#a8a39c;">📅 {{ $isToday ? 'Today, ' : '' }}{{ $dateLabel }}</span>
                                <span style="color:#6a665f;">·</span>
                                <span style="font-size:12px;color:#a8a39c;">🎓 {{ $test->batch_count }} {{ Str::plural('batch', $test->batch_count) }}</span>
                                <span style="color:#6a665f;">·</span>
                                <span style="font-size:12px;color:#a8a39c;">👥 {{ $test->student_count }} students</span>
                                <span style="color:#6a665f;">·</span>
                                <span style="font-size:12px;color:#a8a39c;">📝 {{ $test->total_questions }} questions</span>
                                @if ($test->pattern)
                                    <span style="color:#6a665f;">·</span>
                                    <span style="font-size:12px;color:#a8a39c;">{{ $test->pattern }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Status pill --}}
                        <div style="background:rgba(212,165,116,0.12);border-radius:9999px;padding:6px 12px 6px 10px;display:flex;align-items:center;gap:6px;flex-shrink:0;">
                            <span style="width:6px;height:6px;background:#d4a574;border-radius:50%;display:inline-block;"></span>
                            <span style="font-size:11px;font-weight:500;color:#d4a574;">Ready for upload</span>
                        </div>
                    </div>
                </label>
            @empty
                <div style="background:#14141b;border:1px solid rgba(245,241,232,0.06);border-radius:10px;padding:48px 20px;text-align:center;">
                    <p style="font-size:14px;color:#6a665f;margin:0 0 12px;">No tests ready for upload.</p>
                    <a href="{{ route('sub-admin.tests.create') }}" style="font-size:13px;color:#7a95c8;text-decoration:none;">+ Create a test →</a>
                </div>
            @endforelse
        </div>

        {{-- Footer --}}
        <div style="display:flex;align-items:center;justify-content:space-between;padding-top:4px;">
            <p style="font-size:12px;color:#6a665f;margin:0;">Select the test, then click Continue to upload OMR scan Excel</p>
            <div style="display:flex;gap:10px;align-items:center;">
                <a href="{{ route('sub-admin.dashboard') }}"
                   style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:10px 16px;font-size:13px;font-weight:500;color:#a8a39c;text-decoration:none;">
                    Cancel
                </a>
                <button type="submit" id="continueBtn" disabled
                        style="background:#7a95c8;border:none;border-radius:6px;padding:10px 16px;font-size:13px;font-weight:600;color:#14141b;cursor:not-allowed;display:flex;align-items:center;gap:8px;opacity:0.5;box-shadow:0 2px 6px rgba(122,149,200,0.2);">
                    Continue: Upload Excel <span style="font-weight:700;">→</span>
                </button>
            </div>
        </div>
    </form>

</div>

<script>
function highlightCard(radio) {
    // Reset all cards
    document.querySelectorAll('.test-card').forEach(card => {
        card.style.background    = '#14141b';
        card.style.borderColor   = 'rgba(122,149,200,0.08)';
    });
    document.querySelectorAll('[id^="radio_"]').forEach(dot => {
        dot.style.background    = '#0f0f14';
        dot.style.borderColor   = 'rgba(245,241,232,0.2)';
        dot.innerHTML = '';
    });

    // Highlight selected
    const card  = document.getElementById('card_' + radio.value);
    const rdot  = document.getElementById('radio_' + radio.value);
    card.style.background  = 'rgba(122,149,200,0.1)';
    card.style.borderColor = 'rgba(122,149,200,0.5)';
    card.style.borderWidth = '2px';
    rdot.style.background  = '#7a95c8';
    rdot.style.borderColor = '#7a95c8';
    rdot.innerHTML         = '<span style="width:8px;height:8px;background:#14141b;border-radius:50%;display:inline-block;"></span>';

    // Enable button
    const btn = document.getElementById('continueBtn');
    btn.disabled      = false;
    btn.style.opacity = '1';
    btn.style.cursor  = 'pointer';
}
</script>

@endsection
