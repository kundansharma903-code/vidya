@extends('layouts.owner')
@section('title', $student->name.' — Deep Dive')
@section('breadcrumb', 'Student Deep-Dive')

@push('styles')
<style>
.tab-btn { background:transparent;border:none;padding:10px 16px;font-size:13px;font-weight:500;color:#6a665f;cursor:pointer;border-bottom:2px solid transparent;transition:all 0.15s; }
.tab-btn.active { color:#a392c8;border-bottom-color:#a392c8; }
.tab-btn:hover { color:#a8a39c; }
.tab-pane { display:none; }
.tab-pane.active { display:block; }
</style>
@endpush

@section('content')
<div style="max-width:980px;">

    {{-- Back --}}
    <div style="margin-bottom:18px;">
        <a href="{{ route('owner.rankings') }}" style="font-size:12px;color:#6a665f;text-decoration:none;">← Student Rankings</a>
    </div>

    {{-- Hero card --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:12px;padding:24px;margin-bottom:16px;display:flex;align-items:center;gap:20px;">
        <div style="width:52px;height:52px;border-radius:50%;background:rgba(163,146,200,0.15);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#a392c8;flex-shrink:0;">
            {{ strtoupper(substr($student->name,0,1)) }}
        </div>
        <div style="flex:1;min-width:0;">
            <h1 style="font-size:20px;font-weight:700;color:#f5f1e8;margin:0 0 4px;">{{ $student->name }}</h1>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                <span style="background:#0f0f14;border-radius:4px;padding:2px 8px;font-size:11px;color:#a8a39c;font-family:monospace;">{{ $student->roll_number }}</span>
                <span style="font-size:12px;color:#6a665f;">{{ $student->batch_name }}</span>
            </div>
        </div>
        {{-- Quick KPIs --}}
        @php $mc = $avgMastery >= 70 ? '#7fb685' : ($avgMastery >= 40 ? '#d4a574' : '#c87064'); @endphp
        <div style="display:flex;gap:24px;flex-shrink:0;">
            @foreach([
                ['Tests Taken', $testsCount, '#a8a39c'],
                ['Avg Score',   $avgScore,   '#d4a574'],
                ['Best Rank',   $bestRank ? '#'.$bestRank : '—', '#a392c8'],
                ['Avg Mastery', $avgMastery.'%', $mc],
            ] as [$lbl,$val,$col])
            <div style="text-align:center;">
                <p style="font-size:20px;font-weight:800;color:{{ $col }};margin:0;letter-spacing:-0.4px;">{{ $val }}</p>
                <p style="font-size:9px;color:#6a665f;margin:2px 0 0;text-transform:uppercase;letter-spacing:0.8px;">{{ $lbl }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Tabs --}}
    <div style="border-bottom:1px solid rgba(245,241,232,0.08);margin-bottom:20px;display:flex;gap:0;">
        <button class="tab-btn active" onclick="switchTab('overview',this)">Overview</button>
        <button class="tab-btn" onclick="switchTab('mastery',this)">Topic Mastery</button>
        <button class="tab-btn" onclick="switchTab('weak',this)">Weak Areas</button>
        <button class="tab-btn" onclick="switchTab('tests',this)">All Tests</button>
    </div>

    {{-- TAB: Overview --}}
    <div id="tab-overview" class="tab-pane active">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            {{-- Recent Tests --}}
            <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px;">
                <p style="font-size:11px;font-weight:600;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;">Recent Tests</p>
                @forelse($testHistory->take(5) as $t)
                @php
                    $mp = $t->total_questions > 0 ? $t->total_questions * 4 : 180;
                    $pp = $mp > 0 ? (int)round($t->total_marks / $mp * 100) : 0;
                    $tc = $pp >= 60 ? '#7fb685' : ($pp >= 35 ? '#d4a574' : '#c87064');
                @endphp
                <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                    <div>
                        <p style="font-size:12px;font-weight:500;color:#f5f1e8;margin:0;">{{ $t->test_code }}</p>
                        <p style="font-size:10px;color:#6a665f;margin:2px 0 0;">{{ \Carbon\Carbon::parse($t->test_date)->format('d M Y') }}</p>
                    </div>
                    <div style="text-align:right;">
                        <span style="font-size:16px;font-weight:800;color:{{ $tc }};">{{ $t->total_marks }}</span>
                        <span style="font-size:10px;color:#6a665f;margin-left:4px;">Rank #{{ $t->rank_in_batch }}</span>
                    </div>
                </div>
                @empty
                <p style="font-size:13px;color:#6a665f;text-align:center;padding:20px 0;">No test data yet.</p>
                @endforelse
            </div>
            {{-- Subject Mastery Summary --}}
            <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px;">
                <p style="font-size:11px;font-weight:600;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;">Subject Averages</p>
                @forelse($masteryBySubject as $subjectName => $topics)
                @php
                    $subAvg = collect($topics)->avg('mastery_percentage');
                    $sac = $subAvg >= 70 ? '#7fb685' : ($subAvg >= 40 ? '#d4a574' : '#c87064');
                @endphp
                <div style="margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-size:12px;font-weight:600;color:#f5f1e8;">{{ $subjectName }}</span>
                        <span style="font-size:12px;font-weight:700;color:{{ $sac }};">{{ round($subAvg) }}%</span>
                    </div>
                    <div style="height:4px;background:rgba(245,241,232,0.06);border-radius:2px;">
                        <div style="height:100%;width:{{ min(100,$subAvg) }}%;background:{{ $sac }};border-radius:2px;"></div>
                    </div>
                    <p style="font-size:10px;color:#6a665f;margin:3px 0 0;">{{ count($topics) }} topics covered</p>
                </div>
                @empty
                <p style="font-size:13px;color:#6a665f;text-align:center;padding:20px 0;">No mastery data yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- TAB: Topic Mastery --}}
    <div id="tab-mastery" class="tab-pane">
        @forelse($masteryBySubject as $subjectName => $topics)
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px;margin-bottom:14px;">
            <p style="font-size:12px;font-weight:700;color:#a392c8;text-transform:uppercase;letter-spacing:0.8px;margin:0 0 16px;">{{ $subjectName }}</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                @foreach($topics as $t)
                @php $mp = (float)$t->mastery_percentage; $tc = $mp >= 70 ? '#7fb685' : ($mp >= 40 ? '#d4a574' : '#c87064'); @endphp
                <div style="padding:8px 10px;background:#0f0f14;border-radius:6px;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                        <span style="font-size:11px;color:#a8a39c;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:160px;">{{ $t->topic_code }} · {{ $t->topic_name }}</span>
                        <span style="font-size:11px;font-weight:700;color:{{ $tc }};flex-shrink:0;margin-left:8px;">{{ round($mp) }}%</span>
                    </div>
                    <div style="height:3px;background:rgba(245,241,232,0.06);border-radius:2px;">
                        <div style="height:100%;width:{{ min(100,$mp) }}%;background:{{ $tc }};border-radius:2px;"></div>
                    </div>
                    <p style="font-size:9px;color:#6a665f;margin:3px 0 0;">{{ $t->total_questions_correct ?? 0 }}/{{ $t->total_questions_attempted ?? 0 }} correct</p>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:48px;text-align:center;">
            <p style="font-size:14px;color:#6a665f;">No mastery data available yet for this student.</p>
        </div>
        @endforelse
    </div>

    {{-- TAB: Weak Areas --}}
    <div id="tab-weak" class="tab-pane">
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px;">
            <p style="font-size:11px;font-weight:600;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Weakest Topics — needs immediate attention</p>
            @forelse($weakTopics as $i => $t)
            @php $mp = (float)$t->mastery_percentage; $tc = $mp >= 70 ? '#7fb685' : ($mp >= 40 ? '#d4a574' : '#c87064'); @endphp
            <div style="display:flex;align-items:center;gap:14px;padding:12px 0;border-bottom:1px solid rgba(245,241,232,0.04);">
                <span style="width:24px;height:24px;border-radius:50%;background:rgba(200,112,100,0.12);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#c87064;flex-shrink:0;">{{ $i+1 }}</span>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0 0 2px;">{{ $t->topic_name }}</p>
                    <p style="font-size:10px;color:#6a665f;margin:0;">{{ $t->topic_code }}@isset($t->subject_name) · {{ $t->subject_name }}@endisset · {{ $t->total_questions_correct ?? 0 }}/{{ $t->total_questions_attempted ?? 0 }} correct</p>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <span style="font-size:18px;font-weight:800;color:{{ $tc }};">{{ round($mp) }}%</span>
                    <div style="width:80px;height:4px;background:rgba(245,241,232,0.06);border-radius:2px;margin-top:4px;">
                        <div style="height:100%;width:{{ min(100,$mp) }}%;background:{{ $tc }};border-radius:2px;"></div>
                    </div>
                </div>
            </div>
            @empty
            <p style="font-size:13px;color:#6a665f;text-align:center;padding:32px 0;">No weak topics identified yet.</p>
            @endforelse
        </div>
    </div>

    {{-- TAB: All Tests --}}
    <div id="tab-tests" class="tab-pane">
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid rgba(245,241,232,0.08);">
                        <th style="text-align:left;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:12px 16px;font-weight:700;">Test</th>
                        <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:12px 10px;font-weight:700;">Date</th>
                        <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:12px 10px;font-weight:700;">Score</th>
                        <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:12px 10px;font-weight:700;">Rank</th>
                        <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:12px 10px;font-weight:700;">%ile</th>
                        <th style="text-align:center;font-size:9px;color:#6a665f;text-transform:uppercase;letter-spacing:1px;padding:12px 10px;font-weight:700;">✓/✗</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($testHistory as $t)
                    @php
                        $mp2 = $t->total_questions * 4;
                        $pp2 = $mp2 > 0 ? (int)round($t->total_marks/$mp2*100) : 0;
                        $tc2 = $pp2 >= 60 ? '#7fb685' : ($pp2 >= 35 ? '#d4a574' : '#c87064');
                    @endphp
                    <tr style="border-bottom:1px solid rgba(245,241,232,0.04);"
                        onmouseover="this.style.background='rgba(26,26,36,0.5)'" onmouseout="this.style.background=''">
                        <td style="padding:11px 16px;">
                            <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0;">{{ $t->name }}</p>
                            <p style="font-size:10px;color:#6a665f;font-family:monospace;margin:2px 0 0;">{{ $t->test_code }}</p>
                        </td>
                        <td style="text-align:center;padding:11px 10px;font-size:12px;color:#a8a39c;">{{ \Carbon\Carbon::parse($t->test_date)->format('d M Y') }}</td>
                        <td style="text-align:center;padding:11px 10px;font-size:15px;font-weight:800;color:{{ $tc2 }};">{{ $t->total_marks }}</td>
                        <td style="text-align:center;padding:11px 10px;font-size:12px;color:#d4a574;">#{{ $t->rank_in_batch }}</td>
                        <td style="text-align:center;padding:11px 10px;font-size:12px;color:#a392c8;">{{ round($t->percentile,1) }}%</td>
                        <td style="text-align:center;padding:11px 10px;font-size:12px;">
                            <span style="color:#7fb685;">{{ $t->total_correct }}✓</span>
                            <span style="color:#c87064;margin-left:4px;">{{ $t->total_incorrect }}✗</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:48px;color:#6a665f;">No test history found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
function switchTab(id, btn) {
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-'+id).classList.add('active');
    btn.classList.add('active');
}
</script>
@endpush
@endsection
