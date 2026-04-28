@extends('layouts.academic-head')
@section('title', 'Teacher Effectiveness')
@section('breadcrumb', 'Teacher Effectiveness')

@section('content')
<div style="max-width:1060px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Teacher Effectiveness</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Ranked by class average mastery and topic outcomes</p>
    </div>

    {{-- 4 KPIs --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;">
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Total Teachers</p>
            <p style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0;">{{ count($teacherData) }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Active in institute</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Avg Effectiveness</p>
            <p style="font-size:28px;font-weight:700;color:#7a95c8;letter-spacing:-0.56px;margin:0;">{{ $avgEffectiveness }}%</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Across all teachers</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(127,182,133,0.12);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Top Performer</p>
            <p style="font-size:16px;font-weight:700;color:#7fb685;letter-spacing:-0.32px;margin:0;">{{ $teacherData[0]['teacher']->name ?? '—' }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Score: {{ $teacherData[0]['score'] ?? 0 }}%</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.12);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Needs Attention</p>
            @php $last = end($teacherData); @endphp
            <p style="font-size:16px;font-weight:700;color:#c87064;letter-spacing:-0.32px;margin:0;">{{ $last ? $last['teacher']->name : '—' }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Score: {{ $last ? $last['score'] : 0 }}%</p>
        </div>
    </div>

    {{-- Top 3 Podium --}}
    @if (count($teacherData) >= 1)
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:24px;margin-bottom:18px;">
        <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 20px;">Top Performers</p>
        <div style="display:flex;align-items:flex-end;justify-content:center;gap:16px;">

            {{-- Rank 2 --}}
            @if (isset($teacherData[1]))
            @php $t2 = $teacherData[1]; @endphp
            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;flex:1;max-width:180px;">
                <div style="width:48px;height:48px;border-radius:50%;background:rgba(168,163,156,0.15);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#a8a39c;">
                    {{ strtoupper(substr($t2['teacher']->name,0,1)) }}
                </div>
                <p style="font-size:12px;font-weight:600;color:#f5f1e8;margin:0;text-align:center;">{{ $t2['teacher']->name }}</p>
                <p style="font-size:10px;color:#6a665f;margin:0;">{{ $t2['subjectName'] }}</p>
                <div style="background:#0f0f14;border-radius:8px;padding:8px 12px;text-align:center;width:100%;height:60px;display:flex;flex-direction:column;justify-content:center;border:1px solid rgba(168,163,156,0.15);">
                    <p style="font-size:18px;font-weight:700;color:#a8a39c;margin:0;">{{ $t2['score'] }}%</p>
                    <p style="font-size:9px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.6px;">2nd Place</p>
                </div>
            </div>
            @endif

            {{-- Rank 1 --}}
            @php $t1 = $teacherData[0]; @endphp
            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;flex:1;max-width:200px;">
                <span style="font-size:22px;">🏆</span>
                <div style="width:56px;height:56px;border-radius:50%;background:rgba(127,182,133,0.2);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;color:#7fb685;border:2px solid rgba(127,182,133,0.3);">
                    {{ strtoupper(substr($t1['teacher']->name,0,1)) }}
                </div>
                <p style="font-size:13px;font-weight:700;color:#f5f1e8;margin:0;text-align:center;">{{ $t1['teacher']->name }}</p>
                <p style="font-size:10px;color:#6a665f;margin:0;">{{ $t1['subjectName'] }}</p>
                <div style="background:rgba(127,182,133,0.08);border-radius:8px;padding:10px 14px;text-align:center;width:100%;height:72px;display:flex;flex-direction:column;justify-content:center;border:1px solid rgba(127,182,133,0.2);">
                    <p style="font-size:22px;font-weight:800;color:#7fb685;margin:0;letter-spacing:-0.44px;">{{ $t1['score'] }}%</p>
                    <p style="font-size:9px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.6px;">1st Place</p>
                </div>
            </div>

            {{-- Rank 3 --}}
            @if (isset($teacherData[2]))
            @php $t3 = $teacherData[2]; @endphp
            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;flex:1;max-width:180px;">
                <div style="width:48px;height:48px;border-radius:50%;background:rgba(212,165,116,0.15);display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#d4a574;">
                    {{ strtoupper(substr($t3['teacher']->name,0,1)) }}
                </div>
                <p style="font-size:12px;font-weight:600;color:#f5f1e8;margin:0;text-align:center;">{{ $t3['teacher']->name }}</p>
                <p style="font-size:10px;color:#6a665f;margin:0;">{{ $t3['subjectName'] }}</p>
                <div style="background:#0f0f14;border-radius:8px;padding:8px 12px;text-align:center;width:100%;height:60px;display:flex;flex-direction:column;justify-content:center;border:1px solid rgba(212,165,116,0.15);">
                    <p style="font-size:18px;font-weight:700;color:#d4a574;margin:0;">{{ $t3['score'] }}%</p>
                    <p style="font-size:9px;color:#6a665f;margin:0;text-transform:uppercase;letter-spacing:0.6px;">3rd Place</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Full ranked list --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;margin-bottom:16px;">
        <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:48px 1fr 120px 80px 80px 80px 110px;gap:10px;padding:11px 20px;">
            @foreach(['RANK','TEACHER','SUBJECT','CLASS AVG','WEAK TOPICS','SCORE',''] as $col)
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">{{ $col }}</span>
            @endforeach
        </div>
        @forelse ($teacherData as $i => $td)
            @php
                $sc = $td['score'];
                $scoreColor = $sc >= 70 ? '#7fb685' : ($sc >= 45 ? '#d4a574' : '#c87064');
                $rankBg = $i === 0 ? 'rgba(127,182,133,0.12)' : 'transparent';
            @endphp
            <div style="display:grid;grid-template-columns:48px 1fr 120px 80px 80px 80px 110px;gap:10px;padding:12px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                 onmouseover="this.style.background='rgba(26,26,36,0.5)'" onmouseout="this.style.background=''">

                <div style="display:flex;align-items:center;justify-content:center;">
                    <span style="background:{{ $rankBg }};border-radius:6px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:{{ $i === 0 ? '#7fb685' : '#6a665f' }};">{{ $i+1 }}</span>
                </div>

                <div style="display:flex;align-items:center;gap:10px;min-width:0;">
                    <div style="width:32px;height:32px;border-radius:50%;background:rgba(122,149,200,0.15);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#7a95c8;flex-shrink:0;">
                        {{ strtoupper(substr($td['teacher']->name,0,1)) }}
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:500;color:#f5f1e8;margin:0;">{{ $td['teacher']->name }}</p>
                        <span style="font-size:10px;color:#6a665f;">{{ $td['students'] }} students</span>
                    </div>
                </div>

                <span style="font-size:12px;color:#a8a39c;">{{ $td['subjectName'] }}</span>
                <span style="font-size:13px;font-weight:600;color:{{ $scoreColor }};">{{ $td['classAvg'] }}%</span>
                <span style="font-size:13px;color:#c87064;">{{ $td['weakTopics'] }}</span>
                <span style="font-size:14px;font-weight:700;color:{{ $scoreColor }};">{{ $td['score'] }}</span>
                <a href="{{ route('academic-head.teacher-deep-dive', $td['teacher']->id) }}"
                   style="font-size:11px;font-weight:600;color:#7a95c8;text-decoration:none;background:rgba(122,149,200,0.1);border-radius:4px;padding:4px 8px;white-space:nowrap;">
                    View Insights →
                </a>
            </div>
        @empty
            <div style="padding:48px;text-align:center;"><p style="color:#6a665f;font-size:14px;margin:0;">No teachers found.</p></div>
        @endforelse
    </div>

    {{-- AI Insight --}}
    @if (count($teacherData) > 0)
    @php $lowestTeacher = end($teacherData); @endphp
    <div style="background:rgba(200,112,100,0.05);border:1px solid rgba(200,112,100,0.18);border-radius:8px;padding:16px 20px;display:flex;align-items:flex-start;gap:12px;">
        <span style="font-size:18px;flex-shrink:0;">💡</span>
        <div>
            <p style="font-size:13px;font-weight:600;color:#c87064;margin:0 0 4px;">Needs Attention</p>
            <p style="font-size:12px;color:#a8a39c;margin:0;line-height:1.6;">
                <strong style="color:#f5f1e8;">{{ $lowestTeacher['teacher']->name }}</strong> ({{ $lowestTeacher['subjectName'] }}) has the lowest effectiveness score of {{ $lowestTeacher['score'] }}%,
                with {{ $lowestTeacher['weakTopics'] }} weak topics and a class average of {{ $lowestTeacher['classAvg'] }}%.
                Consider reviewing their topic coverage and providing additional support resources.
            </p>
        </div>
    </div>
    @endif
</div>
@endsection
