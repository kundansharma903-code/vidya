@extends('layouts.academic-head')
@section('title', 'Test Quality')
@section('breadcrumb', 'Test Quality')

@section('content')
<div style="max-width:1060px;">

    <div style="margin-bottom:22px;">
        <h1 style="font-size:26px;font-weight:700;color:#f5f1e8;letter-spacing:-0.52px;margin:0 0 4px;">Test Quality Analysis</h1>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Evaluating topic diversity and coverage alignment across all tests</p>
    </div>

    {{-- 4 KPIs --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px;">
        @php
            $qColor = $overallQuality >= 70 ? '#7fb685' : ($overallQuality >= 45 ? '#d4a574' : '#c87064');
        @endphp
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Overall Quality</p>
            <p style="font-size:28px;font-weight:700;color:{{ $qColor }};letter-spacing:-0.56px;margin:0;">{{ $overallQuality }}%</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Weighted score</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Avg Topic Diversity</p>
            <p style="font-size:28px;font-weight:700;color:#7a95c8;letter-spacing:-0.56px;margin:0;">{{ $avgDiversity }}%</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Topics covered per test</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Tests Analyzed</p>
            <p style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0;">{{ count($testData) }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Total records</p>
        </div>
        <div style="background:#14141b;border:1px solid rgba(200,112,100,0.12);border-radius:8px;padding:16px 18px;">
            <p style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 6px;">Below Standard</p>
            <p style="font-size:28px;font-weight:700;color:#c87064;letter-spacing:-0.56px;margin:0;">{{ $belowStandard }}</p>
            <p style="font-size:11px;color:#6a665f;margin:0;">Grade C tests</p>
        </div>
    </div>

    {{-- Topic Diversity Score --}}
    <div style="display:grid;grid-template-columns:220px 1fr;gap:16px;margin-bottom:18px;">

        {{-- Circular metric --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:22px;display:flex;flex-direction:column;align-items:center;justify-content:center;">
            <p style="font-size:10px;font-weight:600;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 14px;">Topic Diversity</p>
            @php
                $circ2 = 339.3;
                $off2  = $circ2 - ($avgDiversity / 100 * $circ2);
                $dc    = $avgDiversity >= 70 ? '#7fb685' : ($avgDiversity >= 45 ? '#d4a574' : '#c87064');
            @endphp
            <div style="position:relative;width:110px;height:110px;">
                <svg width="110" height="110" viewBox="0 0 120 120" style="transform:rotate(-90deg);">
                    <circle cx="60" cy="60" r="54" fill="none" stroke="rgba(245,241,232,0.06)" stroke-width="10"/>
                    <circle cx="60" cy="60" r="54" fill="none" stroke="{{ $dc }}" stroke-width="10"
                            stroke-dasharray="{{ $circ2 }}" stroke-dashoffset="{{ $off2 }}" stroke-linecap="round"/>
                </svg>
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <span style="font-size:26px;font-weight:800;color:{{ $dc }};letter-spacing:-0.52px;">{{ $avgDiversity }}%</span>
                </div>
            </div>
            <p style="font-size:11px;color:#6a665f;margin:10px 0 0;text-align:center;">Target: 80%+</p>
        </div>

        {{-- Grade distribution --}}
        <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;padding:20px 22px;">
            <p style="font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:1px;text-transform:uppercase;margin:0 0 16px;">Quality Grade Distribution</p>
            @php
                $gradeCounts = ['A+'=>0,'A'=>0,'B'=>0,'C'=>0];
                foreach($testData as $td) $gradeCounts[$td['grade']]++;
                $total = max(1, count($testData));
            @endphp
            <div style="display:flex;flex-direction:column;gap:10px;">
                @foreach([['A+','Excellent (≥80% diversity)','#7fb685'],['A','Good (60–79%)','#7a95c8'],['B','Average (40–59%)','#d4a574'],['C','Below Standard (<40%)','#c87064']] as [$g,$desc,$gc])
                    @php $cnt = $gradeCounts[$g]; $pct = (int)round($cnt/$total*100); @endphp
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span style="font-size:13px;font-weight:700;color:{{ $gc }};min-width:24px;">{{ $g }}</span>
                        <div style="flex:1;height:8px;background:rgba(245,241,232,0.06);border-radius:4px;overflow:hidden;">
                            <div style="height:100%;background:{{ $gc }};width:{{ $pct }}%;"></div>
                        </div>
                        <span style="font-size:12px;color:#a8a39c;min-width:80px;">{{ $cnt }} test{{ $cnt !== 1 ? 's' : '' }} · {{ $pct }}%</span>
                        <span style="font-size:11px;color:#6a665f;">{{ $desc }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tests table --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;overflow:hidden;">
        <div style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.06);display:grid;grid-template-columns:120px 1fr 100px 100px 80px;gap:10px;padding:11px 20px;">
            @foreach(['CODE','NAME','DATE','DIVERSITY','GRADE'] as $col)
                <span style="font-size:10px;font-weight:500;color:#a8a39c;letter-spacing:1px;">{{ $col }}</span>
            @endforeach
        </div>
        @forelse ($testData as $td)
            <div style="display:grid;grid-template-columns:120px 1fr 100px 100px 80px;gap:10px;padding:12px 20px;align-items:center;border-bottom:1px solid rgba(245,241,232,0.04);"
                 onmouseover="this.style.background='rgba(26,26,36,0.5)'" onmouseout="this.style.background=''">
                <span style="font-size:11px;font-weight:600;color:#a8a39c;font-family:monospace;">{{ $td['code'] }}</span>
                <span style="font-size:13px;color:#f5f1e8;">{{ $td['name'] }}</span>
                <span style="font-size:12px;color:#a8a39c;">{{ \Carbon\Carbon::parse($td['date'])->format('d M Y') }}</span>
                <div style="display:flex;align-items:center;gap:8px;">
                    <div style="flex:1;height:4px;background:rgba(245,241,232,0.06);border-radius:2px;overflow:hidden;">
                        <div style="height:100%;background:{{ $td['gradeColor'] }};width:{{ $td['diversity'] }}%;"></div>
                    </div>
                    <span style="font-size:11px;font-weight:600;color:{{ $td['gradeColor'] }};">{{ $td['diversity'] }}%</span>
                </div>
                <span style="background:rgba({{ $td['grade']==='A+'?'127,182,133':($td['grade']==='A'?'122,149,200':($td['grade']==='B'?'212,165,116':'200,112,100')) }},0.12);border-radius:9999px;padding:3px 10px;font-size:12px;font-weight:700;color:{{ $td['gradeColor'] }};display:inline-block;">{{ $td['grade'] }}</span>
            </div>
        @empty
            <div style="padding:48px;text-align:center;"><p style="color:#6a665f;font-size:14px;margin:0;">No analyzed tests yet.</p></div>
        @endforelse
    </div>

    {{-- AI Recommendation --}}
    <div style="background:rgba(122,149,200,0.06);border:1px solid rgba(122,149,200,0.15);border-radius:8px;padding:16px 20px;margin-top:16px;display:flex;align-items:flex-start;gap:12px;">
        <span style="font-size:18px;flex-shrink:0;">💡</span>
        <div>
            <p style="font-size:13px;font-weight:600;color:#7a95c8;margin:0 0 4px;">Recommendation for Typists</p>
            <p style="font-size:12px;color:#a8a39c;margin:0;line-height:1.6;">
                @if ($avgDiversity < 60)
                    Current tests cover {{ $avgDiversity }}% of topics. Aim for 80%+ by increasing topic distribution across question papers. Focus on subjects with zero coverage.
                @else
                    Topic diversity is healthy at {{ $avgDiversity }}%. Maintain coverage alignment with the curriculum schedule and avoid topic repetition in consecutive tests.
                @endif
            </p>
        </div>
    </div>
</div>
@endsection
