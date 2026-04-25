@extends('layouts.admin')
@section('title', 'Curriculum Tree')

@section('content')
@php
$subjectColors = [
    'P' => ['bg' => 'rgba(95,126,180,0.2)',   'color' => '#7a95c8'],
    'C' => ['bg' => 'rgba(127,182,133,0.15)', 'color' => '#7fb685'],
    'B' => ['bg' => 'rgba(163,146,200,0.15)', 'color' => '#a392c8'],
    'Z' => ['bg' => 'rgba(200,112,100,0.15)', 'color' => '#c87064'],
    'M' => ['bg' => 'rgba(212,165,116,0.15)', 'color' => '#d4a574'],
    'E' => ['bg' => 'rgba(106,176,178,0.15)', 'color' => '#6ab0b2'],
];
$defaultColor = ['bg' => 'rgba(122,149,200,0.15)', 'color' => '#7a95c8'];
@endphp

{{-- Topbar --}}
<div style="background:#08080a;border-bottom:1px solid rgba(245,241,232,0.06);height:60px;display:flex;align-items:center;justify-content:space-between;padding:0 24px 0 32px;flex-shrink:0;">
    <div style="display:flex;gap:8px;align-items:center;">
        <span style="color:#6a665f;font-size:13px;">Admin</span>
        <span style="color:#6a665f;font-size:13px;">/</span>
        <span style="color:#f5f1e8;font-size:14px;font-weight:600;">Curriculum</span>
    </div>
    <div style="display:flex;gap:12px;align-items:center;">
        <div style="background:#14141b;border-radius:6px;padding:8px 10px;font-size:13px;color:#a8a39c;">🔔</div>
        <div style="background:#14141b;border-radius:18px;display:flex;align-items:center;gap:10px;padding:4px 12px 4px 4px;">
            @php
                $u = Auth::user();
                $initials = strtoupper(substr($u->name, 0, 1) . (strpos($u->name, ' ') !== false ? substr($u->name, strpos($u->name, ' ') + 1, 1) : ''));
            @endphp
            <div style="width:28px;height:28px;border-radius:14px;background:#5f7eb4;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;color:#f5f1e8;">{{ $initials }}</div>
            <div>
                <div style="font-size:12px;font-weight:500;color:#f5f1e8;">{{ $u->name }}</div>
                <div style="font-size:10px;color:#6a665f;">{{ ucfirst($u->role) }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Page Content --}}
<div style="padding:32px;display:flex;flex-direction:column;gap:24px;overflow-y:auto;flex:1;">

    {{-- Flash --}}
    @if(session('success'))
    <div style="background:rgba(127,182,133,0.1);border:1px solid rgba(127,182,133,0.25);border-radius:6px;padding:10px 16px;font-size:13px;color:#7fb685;">
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div style="background:rgba(224,82,82,0.08);border:1px solid rgba(224,82,82,0.2);border-radius:6px;padding:10px 16px;font-size:13px;color:#e05252;">
        {{ $errors->first() }}
    </div>
    @endif

    {{-- Page Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;">
        <div>
            <h1 style="font-size:28px;font-weight:700;color:#f5f1e8;letter-spacing:-0.56px;margin:0 0 6px 0;">Curriculum Tree</h1>
            <div style="display:flex;gap:10px;align-items:center;font-size:13px;">
                <span style="font-weight:500;color:#f5f1e8;">{{ number_format($stats->subtopic_count ?? 0) }} total subtopics</span>
                <span style="color:#6a665f;">·</span>
                <span style="color:#a8a39c;">{{ $subjects->count() }} subjects</span>
                @if(($stats->subtopic_count ?? 0) > 0)
                <span style="color:#6a665f;">·</span>
                <span style="color:#7a95c8;">Pre-loaded NEET + JEE</span>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:10px;align-items:center;">
            <button onclick="alert('PDF download coming soon.')" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;cursor:pointer;display:flex;gap:8px;align-items:center;">
                <span style="color:#a8a39c;">↓</span> Download Reference PDF
            </button>
            <button onclick="alert('Excel import coming soon.')" style="background:#14141b;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;cursor:pointer;display:flex;gap:8px;align-items:center;">
                <span style="color:#a8a39c;">↑</span> Import Excel
            </button>
            <button onclick="openAddModal('chapter', null, null, {{ $activeSubjectId }}, '')"
                style="background:#7a95c8;border:none;border-radius:6px;padding:10px 16px;font-size:13px;font-weight:600;color:#14141b;cursor:pointer;display:flex;gap:6px;align-items:center;box-shadow:0 2px 6px rgba(122,149,200,0.2);">
                <span style="font-size:14px;font-weight:700;">+</span> Add Chapter
            </button>
        </div>
    </div>

    @if($subjects->isEmpty())
    {{-- No subjects state --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;padding:80px 32px;text-align:center;">
        <div style="font-size:32px;margin-bottom:16px;">📚</div>
        <p style="font-size:16px;font-weight:600;color:#f5f1e8;margin:0 0 8px 0;">No subjects yet</p>
        <p style="font-size:13px;color:#a8a39c;margin:0;">Create subjects first, then build the curriculum tree.</p>
    </div>
    @else

    {{-- Subject Tabs --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;display:flex;overflow:hidden;flex-shrink:0;">
        @foreach($subjects as $subject)
        @php
            $sc = $subjectColors[strtoupper(substr($subject->code ?? $subject->name, 0, 1))] ?? $defaultColor;
            $isActive = $subject->id === $activeSubjectId;
        @endphp
        <a href="{{ route('admin.curriculum', ['subject_id' => $subject->id]) }}"
           style="display:flex;gap:8px;align-items:center;padding:14px 20px;text-decoration:none;
                  {{ $isActive ? 'background:rgba(122,149,200,0.1);border-bottom:2px solid #7a95c8;' : 'border-bottom:2px solid transparent;' }}">
            <div style="width:22px;height:22px;border-radius:4px;background:{{ $isActive ? $sc['bg'] : 'rgba(245,241,232,0.06)' }};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:{{ $isActive ? $sc['color'] : '#6a665f' }};">
                {{ strtoupper(substr($subject->code ?? $subject->name, 0, 1)) }}
            </div>
            <span style="font-size:13px;font-weight:{{ $isActive ? '600' : '500' }};color:{{ $isActive ? '#f5f1e8' : '#a8a39c' }};white-space:nowrap;">
                {{ $subject->name }}
            </span>
        </a>
        @endforeach
    </div>

    {{-- Search --}}
    <div>
        <div style="background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;display:flex;align-items:center;gap:10px;padding:11px 14px;">
            <span style="color:#6a665f;font-size:14px;">⌕</span>
            <input id="treeSearch" type="text" placeholder="Search chapters, topics, subtopics, or codes (e.g. P-MEC-KIN-01)…"
                   oninput="filterTree(this.value)"
                   style="background:none;border:none;outline:none;font-size:13px;color:#f5f1e8;width:100%;font-family:inherit;"
                   onfocus="this.closest('div').style.borderColor='rgba(122,149,200,0.4)'"
                   onblur="this.closest('div').style.borderColor='rgba(245,241,232,0.1)'">
        </div>
    </div>

    {{-- Tree Panel --}}
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:8px;overflow:hidden;">

        @if($chapters->isEmpty())
        {{-- Empty subject state --}}
        <div style="padding:60px 32px;text-align:center;">
            <div style="font-size:28px;margin-bottom:12px;">🌱</div>
            <p style="font-size:15px;font-weight:600;color:#f5f1e8;margin:0 0 6px 0;">No chapters yet for {{ $activeSubject?->name ?? 'this subject' }}</p>
            <p style="font-size:13px;color:#a8a39c;margin:0 0 20px 0;">Start building the curriculum tree by adding the first chapter.</p>
            <button onclick="openAddModal('chapter', null, null, {{ $activeSubjectId }}, '')"
                style="background:#7a95c8;border:none;border-radius:6px;padding:10px 20px;font-size:13px;font-weight:600;color:#14141b;cursor:pointer;">
                + Add First Chapter
            </button>
        </div>
        @else

        @php $isLastChapter = false; @endphp
        @foreach($chapters as $chIdx => $chapter)
        @php
            $chTopics = $topicsByChapter[$chapter->id] ?? collect();
            $topicCount = $chTopics->count();
            $subtopicCount = 0;
            foreach ($chTopics as $t) {
                $subtopicCount += ($subtopicsByTopic[$t->id] ?? collect())->count();
            }
            $isLastChapter = ($chIdx === $chapters->count() - 1) && $topicCount === 0;
        @endphp

        {{-- Chapter Row --}}
        <div class="tree-row" data-level="chapter" data-id="{{ $chapter->id }}"
             data-search="{{ strtolower($chapter->name . ' ' . $chapter->code . ' ' . $chapter->full_code) }}"
             style="background:#1a1a24;border-bottom:1px solid rgba(245,241,232,0.05);height:52px;display:flex;align-items:center;gap:12px;padding:0 20px;">

            <div onclick="toggleChapter({{ $chapter->id }})" id="ch-chevron-{{ $chapter->id }}"
                 style="width:16px;height:16px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#a8a39c;font-size:12px;flex-shrink:0;">
                {{ $topicCount > 0 ? '▾' : '▸' }}
            </div>

            <div style="background:#0f0f14;height:26px;width:140px;flex-shrink:0;border-radius:4px;padding:0 8px;display:flex;align-items:center;overflow:hidden;">
                <span style="font-size:11px;font-weight:500;color:#a8a39c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $chapter->full_code }}</span>
            </div>

            <div style="flex:1;display:flex;align-items:center;gap:8px;min-width:0;overflow:hidden;">
                <span style="font-size:14px;font-weight:600;color:#f5f1e8;white-space:nowrap;">{{ $chapter->name }}</span>
                @if($topicCount > 0 || $subtopicCount > 0)
                <span style="font-size:11px;color:#6a665f;white-space:nowrap;">
                    · {{ $topicCount }} {{ Str::plural('topic', $topicCount) }}{{ $subtopicCount > 0 ? ' · ' . $subtopicCount . ' ' . Str::plural('subtopic', $subtopicCount) : '' }}
                </span>
                @endif
            </div>

            @if($chapter->weightage !== null)
            <div style="background:rgba(122,149,200,0.12);border-radius:4px;padding:3px 10px 3px 8px;flex-shrink:0;">
                <span style="font-size:11px;font-weight:500;color:#7a95c8;">{{ number_format($chapter->weightage, 0) }}%</span>
            </div>
            @endif

            <div style="display:flex;gap:4px;align-items:center;flex-shrink:0;width:90px;">
                <button onclick="openAddModal('topic', {{ $chapter->id }}, '{{ addslashes($chapter->name) }}', {{ $chapter->subject_id }}, '{{ $chapter->full_code }}')"
                    title="Add topic"
                    style="width:28px;height:28px;border:none;background:none;border-radius:4px;cursor:pointer;font-size:13px;color:#7a95c8;display:flex;align-items:center;justify-content:center;"
                    onmouseover="this.style.background='rgba(122,149,200,0.1)'" onmouseout="this.style.background='none'">+</button>
                <button onclick="openEditModal({{ $chapter->id }}, '{{ addslashes($chapter->name) }}', '{{ $chapter->code }}', '{{ $chapter->level }}', {{ $chapter->subject_id }}, {{ $chapter->weightage ?? 'null' }})"
                    title="Edit"
                    style="width:28px;height:28px;border:none;background:none;border-radius:4px;cursor:pointer;font-size:13px;color:#6a665f;display:flex;align-items:center;justify-content:center;"
                    onmouseover="this.style.background='rgba(245,241,232,0.05)'" onmouseout="this.style.background='none'">✎</button>
                <button onclick="confirmDelete({{ $chapter->id }}, '{{ addslashes($chapter->name) }}', '{{ $chapter->level }}', {{ $chapter->subject_id }})"
                    title="Delete"
                    style="width:28px;height:28px;border:none;background:none;border-radius:4px;cursor:pointer;font-size:13px;color:#6a665f;display:flex;align-items:center;justify-content:center;"
                    onmouseover="this.style.color='#e05252';this.style.background='rgba(224,82,82,0.08)'" onmouseout="this.style.color='#6a665f';this.style.background='none'">×</button>
            </div>
        </div>

        {{-- Topics group --}}
        <div id="ch-group-{{ $chapter->id }}" style="{{ $topicCount === 0 ? 'display:none;' : '' }}">
            @foreach($chTopics as $tIdx => $topic)
            @php
                $tSubtopics = $subtopicsByTopic[$topic->id] ?? collect();
                $stCount = $tSubtopics->count();
                $isLastTopic = ($tIdx === $chTopics->count() - 1) && $stCount === 0;
            @endphp

            {{-- Topic Row --}}
            <div class="tree-row" data-level="topic" data-id="{{ $topic->id }}"
                 data-search="{{ strtolower($topic->name . ' ' . $topic->code . ' ' . $topic->full_code) }}"
                 style="border-bottom:1px solid rgba(245,241,232,0.05);height:52px;display:flex;align-items:center;gap:12px;padding:0 20px 0 48px;">

                <div onclick="toggleTopic({{ $topic->id }})" id="topic-chevron-{{ $topic->id }}"
                     style="width:16px;height:16px;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#a8a39c;font-size:12px;flex-shrink:0;">
                    {{ $stCount > 0 ? '▾' : '▸' }}
                </div>

                <div style="background:#0f0f14;height:26px;width:140px;flex-shrink:0;border-radius:4px;padding:0 8px;display:flex;align-items:center;overflow:hidden;">
                    <span style="font-size:11px;font-weight:500;color:#a8a39c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $topic->full_code }}</span>
                </div>

                <div style="flex:1;display:flex;align-items:center;gap:8px;min-width:0;overflow:hidden;">
                    <span style="font-size:14px;font-weight:500;color:#f5f1e8;white-space:nowrap;">{{ $topic->name }}</span>
                    @if($stCount > 0)
                    <span style="font-size:11px;color:#6a665f;white-space:nowrap;">· {{ $stCount }} {{ Str::plural('subtopic', $stCount) }}</span>
                    @endif
                </div>

                @if($topic->weightage !== null)
                <div style="background:rgba(122,149,200,0.12);border-radius:4px;padding:3px 10px 3px 8px;flex-shrink:0;">
                    <span style="font-size:11px;font-weight:500;color:#7a95c8;">{{ number_format($topic->weightage, 0) }}%</span>
                </div>
                @endif

                <div style="display:flex;gap:4px;align-items:center;flex-shrink:0;width:90px;">
                    <button onclick="openAddModal('subtopic', {{ $topic->id }}, '{{ addslashes($topic->name) }}', {{ $topic->subject_id }}, '{{ $topic->full_code }}')"
                        title="Add subtopic"
                        style="width:28px;height:28px;border:none;background:none;border-radius:4px;cursor:pointer;font-size:13px;color:#7a95c8;display:flex;align-items:center;justify-content:center;"
                        onmouseover="this.style.background='rgba(122,149,200,0.1)'" onmouseout="this.style.background='none'">+</button>
                    <button onclick="openEditModal({{ $topic->id }}, '{{ addslashes($topic->name) }}', '{{ $topic->code }}', '{{ $topic->level }}', {{ $topic->subject_id }}, {{ $topic->weightage ?? 'null' }})"
                        title="Edit"
                        style="width:28px;height:28px;border:none;background:none;border-radius:4px;cursor:pointer;font-size:13px;color:#6a665f;display:flex;align-items:center;justify-content:center;"
                        onmouseover="this.style.background='rgba(245,241,232,0.05)'" onmouseout="this.style.background='none'">✎</button>
                    <button onclick="confirmDelete({{ $topic->id }}, '{{ addslashes($topic->name) }}', '{{ $topic->level }}', {{ $topic->subject_id }})"
                        title="Delete"
                        style="width:28px;height:28px;border:none;background:none;border-radius:4px;cursor:pointer;font-size:13px;color:#6a665f;display:flex;align-items:center;justify-content:center;"
                        onmouseover="this.style.color='#e05252';this.style.background='rgba(224,82,82,0.08)'" onmouseout="this.style.color='#6a665f';this.style.background='none'">×</button>
                </div>
            </div>

            {{-- Subtopics group --}}
            <div id="topic-group-{{ $topic->id }}" style="{{ $stCount === 0 ? 'display:none;' : '' }}">
                @foreach($tSubtopics as $stIdx => $subtopic)
                @php $isLastRow = ($stIdx === $tSubtopics->count() - 1); @endphp
                <div class="tree-row" data-level="subtopic" data-id="{{ $subtopic->id }}"
                     data-search="{{ strtolower($subtopic->name . ' ' . $subtopic->code . ' ' . $subtopic->full_code) }}"
                     style="border-bottom:1px solid rgba(245,241,232,0.05);height:48px;display:flex;align-items:center;gap:12px;padding:0 20px 0 76px;">

                    <div style="width:16px;height:16px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;font-weight:700;color:#6a665f;">·</div>

                    <div style="background:#0f0f14;height:26px;width:140px;flex-shrink:0;border-radius:4px;padding:0 8px;display:flex;align-items:center;overflow:hidden;">
                        <span style="font-size:11px;font-weight:500;color:#a8a39c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $subtopic->full_code }}</span>
                    </div>

                    <div style="flex:1;display:flex;align-items:center;min-width:0;overflow:hidden;">
                        <span style="font-size:13px;color:#a8a39c;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $subtopic->name }}</span>
                    </div>

                    <div style="width:1px;flex-shrink:0;"></div>

                    <div style="display:flex;gap:4px;align-items:center;flex-shrink:0;width:90px;margin-left:auto;">
                        <div style="width:28px;"></div>{{-- spacer for + column --}}
                        <button onclick="openEditModal({{ $subtopic->id }}, '{{ addslashes($subtopic->name) }}', '{{ $subtopic->code }}', '{{ $subtopic->level }}', {{ $subtopic->subject_id }}, {{ $subtopic->weightage ?? 'null' }})"
                            title="Edit"
                            style="width:28px;height:28px;border:none;background:none;border-radius:4px;cursor:pointer;font-size:13px;color:#6a665f;display:flex;align-items:center;justify-content:center;"
                            onmouseover="this.style.background='rgba(245,241,232,0.05)'" onmouseout="this.style.background='none'">✎</button>
                        <button onclick="confirmDelete({{ $subtopic->id }}, '{{ addslashes($subtopic->name) }}', '{{ $subtopic->level }}', {{ $subtopic->subject_id }})"
                            title="Delete"
                            style="width:28px;height:28px;border:none;background:none;border-radius:4px;cursor:pointer;font-size:13px;color:#6a665f;display:flex;align-items:center;justify-content:center;"
                            onmouseover="this.style.color='#e05252';this.style.background='rgba(224,82,82,0.08)'" onmouseout="this.style.color='#6a665f';this.style.background='none'">×</button>
                    </div>
                </div>
                @endforeach
            </div>
            {{-- End subtopics --}}

            @endforeach
        </div>
        {{-- End topics group --}}

        @endforeach
        @endif
        {{-- End tree rows --}}

    </div>
    {{-- End tree panel --}}

    @endif
    {{-- End subjects exist check --}}

</div>
{{-- End page content --}}


{{-- ===== ADD NODE MODAL ===== --}}
<div id="addModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;width:100%;max-width:520px;margin:0 16px;">
        <div style="padding:20px 24px 0;display:flex;justify-content:space-between;align-items:flex-start;">
            <div>
                <h2 id="addModalTitle" style="font-size:18px;font-weight:700;color:#f5f1e8;margin:0 0 4px 0;">Add Chapter</h2>
                <p id="addModalSubtitle" style="font-size:12px;color:#6a665f;margin:0;"></p>
            </div>
            <button onclick="closeAddModal()" style="background:none;border:none;cursor:pointer;font-size:18px;color:#6a665f;padding:0;line-height:1;" onmouseover="this.style.color='#f5f1e8'" onmouseout="this.style.color='#6a665f'">×</button>
        </div>

        <form id="addForm" method="POST" action="{{ route('admin.curriculum.store') }}" style="padding:20px 24px 24px;display:flex;flex-direction:column;gap:14px;">
            @csrf
            <input type="hidden" name="level"      id="addLevel">
            <input type="hidden" name="parent_id"  id="addParentId">
            <input type="hidden" name="subject_id" id="addSubjectId">

            {{-- Parent info (shown for topic/subtopic) --}}
            <div id="addParentInfo" style="display:none;background:rgba(122,149,200,0.06);border:1px solid rgba(122,149,200,0.15);border-radius:6px;padding:8px 12px;">
                <span style="font-size:11px;color:#7a95c8;font-weight:500;text-transform:uppercase;letter-spacing:0.5px;">Adding under</span>
                <p id="addParentName" style="font-size:13px;color:#f5f1e8;margin:2px 0 0 0;font-weight:500;"></p>
            </div>

            <div>
                <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;">Name *</label>
                <input type="text" name="name" id="addName" required placeholder="e.g. Kinematics"
                    style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;font-family:inherit;"
                    onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;">Code *</label>
                    <input type="text" name="code" id="addCode" required placeholder="e.g. MEC"
                        oninput="this.value=this.value.toUpperCase();updateCodePreview()"
                        style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;font-family:monospace;"
                        onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                    <div id="addCodePreview" style="margin-top:4px;font-size:11px;color:#6a665f;"></div>
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;">Weightage <span style="color:#6a665f;text-transform:none;font-weight:400;letter-spacing:0;">(optional %)</span></label>
                    <input type="number" name="weightage" id="addWeightage" placeholder="e.g. 20" min="0" max="100" step="0.1"
                        style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;font-family:inherit;"
                        onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:4px;">
                <button type="button" onclick="closeAddModal()"
                    style="background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:9px 18px;font-size:13px;color:#a8a39c;cursor:pointer;font-family:inherit;">Cancel</button>
                <button type="submit" id="addSubmitBtn"
                    style="background:#7a95c8;border:none;border-radius:6px;padding:9px 18px;font-size:13px;font-weight:600;color:#14141b;cursor:pointer;font-family:inherit;">Add Chapter</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== EDIT MODAL ===== --}}
<div id="editModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#14141b;border:1px solid rgba(245,241,232,0.08);border-radius:10px;width:100%;max-width:520px;margin:0 16px;">
        <div style="padding:20px 24px 0;display:flex;justify-content:space-between;align-items:flex-start;">
            <div>
                <h2 id="editModalTitle" style="font-size:18px;font-weight:700;color:#f5f1e8;margin:0 0 4px 0;">Edit</h2>
                <p id="editModalSub" style="font-size:12px;color:#6a665f;margin:0;"></p>
            </div>
            <button onclick="closeEditModal()" style="background:none;border:none;cursor:pointer;font-size:18px;color:#6a665f;padding:0;line-height:1;" onmouseover="this.style.color='#f5f1e8'" onmouseout="this.style.color='#6a665f'">×</button>
        </div>

        <form id="editForm" method="POST" style="padding:20px 24px 24px;display:flex;flex-direction:column;gap:14px;">
            @csrf
            @method('PUT')
            <input type="hidden" name="subject_id" id="editSubjectId">

            <div>
                <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;">Name *</label>
                <input type="text" name="name" id="editName" required
                    style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;font-family:inherit;"
                    onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;">Code *</label>
                    <input type="text" name="code" id="editCode" required
                        oninput="this.value=this.value.toUpperCase()"
                        style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;font-family:monospace;"
                        onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                </div>
                <div>
                    <label style="display:block;font-size:11px;font-weight:500;color:#a8a39c;letter-spacing:0.44px;text-transform:uppercase;margin-bottom:6px;">Weightage <span style="color:#6a665f;text-transform:none;font-weight:400;letter-spacing:0;">(optional %)</span></label>
                    <input type="number" name="weightage" id="editWeightage" placeholder="e.g. 20" min="0" max="100" step="0.1"
                        style="width:100%;background:#0f0f14;border:1px solid rgba(245,241,232,0.10);border-radius:6px;padding:10px 14px;font-size:13px;color:#f5f1e8;outline:none;box-sizing:border-box;font-family:inherit;"
                        onfocus="this.style.borderColor='rgba(122,149,200,0.4)'" onblur="this.style.borderColor='rgba(245,241,232,0.10)'">
                </div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:4px;">
                <button type="button" onclick="closeEditModal()"
                    style="background:#0f0f14;border:1px solid rgba(245,241,232,0.1);border-radius:6px;padding:9px 18px;font-size:13px;color:#a8a39c;cursor:pointer;font-family:inherit;">Cancel</button>
                <button type="submit"
                    style="background:#7a95c8;border:none;border-radius:6px;padding:9px 18px;font-size:13px;font-weight:600;color:#14141b;cursor:pointer;font-family:inherit;">Save Changes</button>
            </div>
        </form>
    </div>
</div>

{{-- Hidden delete form --}}
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
// ===== State =====
var expandState = {};
var addContext  = {}; // {level, parentId, parentFullCode, subjectId}

// ===== Expand / Collapse =====
function toggleChapter(chId) {
    var group   = document.getElementById('ch-group-' + chId);
    var chevron = document.getElementById('ch-chevron-' + chId);
    if (!group) return;
    var key = 'ch-' + chId;
    var nowHidden = group.style.display === 'none';
    expandState[key] = nowHidden;
    group.style.display   = nowHidden ? '' : 'none';
    chevron.textContent   = nowHidden ? '▾' : '▸';
    chevron.style.color   = nowHidden ? '#a8a39c' : '#6a665f';
}

function toggleTopic(topicId) {
    var group   = document.getElementById('topic-group-' + topicId);
    var chevron = document.getElementById('topic-chevron-' + topicId);
    if (!group) return;
    var key = 'topic-' + topicId;
    var nowHidden = group.style.display === 'none';
    expandState[key] = nowHidden;
    group.style.display  = nowHidden ? '' : 'none';
    chevron.textContent  = nowHidden ? '▾' : '▸';
    chevron.style.color  = nowHidden ? '#a8a39c' : '#6a665f';
}

// ===== Search =====
function filterTree(query) {
    query = query.toLowerCase().trim();

    // Reset all
    document.querySelectorAll('.tree-row').forEach(r => r.style.removeProperty('display'));
    document.querySelectorAll('[id^="ch-group-"], [id^="topic-group-"]').forEach(g => g.style.removeProperty('display'));

    if (!query) return;

    // Bottom-up: subtopics first
    document.querySelectorAll('.tree-row[data-level="subtopic"]').forEach(r => {
        r.dataset.matched = r.dataset.search.includes(query) ? '1' : '0';
        r.style.display   = r.dataset.matched === '1' ? '' : 'none';
    });

    // Topics: match if self or any child subtopic matches
    document.querySelectorAll('.tree-row[data-level="topic"]').forEach(r => {
        var selfMatch = r.dataset.search.includes(query);
        var grp = document.getElementById('topic-group-' + r.dataset.id);
        var childMatch = grp && Array.from(grp.querySelectorAll('.tree-row[data-level="subtopic"]')).some(s => s.dataset.matched === '1');
        var match = selfMatch || childMatch;
        r.dataset.matched = match ? '1' : '0';
        r.style.display   = match ? '' : 'none';
        if (grp) grp.style.display = match ? '' : 'none';
    });

    // Chapters: match if self or any child topic matches
    document.querySelectorAll('.tree-row[data-level="chapter"]').forEach(r => {
        var selfMatch = r.dataset.search.includes(query);
        var grp = document.getElementById('ch-group-' + r.dataset.id);
        var childMatch = grp && Array.from(grp.querySelectorAll('.tree-row[data-level="topic"]')).some(t => t.dataset.matched === '1');
        var match = selfMatch || childMatch;
        r.style.display   = match ? '' : 'none';
        if (grp) grp.style.display = match ? '' : 'none';
    });
}

// ===== Add Modal =====
var levelLabels = { chapter: 'Chapter', topic: 'Topic', subtopic: 'Subtopic' };
var codePlaceholders = { chapter: 'e.g. MEC', topic: 'e.g. KIN', subtopic: 'e.g. 01' };

function openAddModal(level, parentId, parentName, subjectId, parentFullCode) {
    addContext = { level: level, parentId: parentId, parentFullCode: parentFullCode, subjectId: subjectId };

    var label = levelLabels[level] || level;
    document.getElementById('addModalTitle').textContent = 'Add ' + label;
    document.getElementById('addSubmitBtn').textContent  = 'Add ' + label;
    document.getElementById('addLevel').value     = level;
    document.getElementById('addParentId').value  = parentId || '';
    document.getElementById('addSubjectId').value = subjectId;
    document.getElementById('addName').value      = '';
    document.getElementById('addCode').value      = '';
    document.getElementById('addWeightage').value = '';
    document.getElementById('addCodePreview').textContent = '';

    var parentInfo = document.getElementById('addParentInfo');
    if (parentId && parentName) {
        document.getElementById('addParentName').textContent = parentName;
        parentInfo.style.display = '';
        document.getElementById('addModalSubtitle').textContent = 'Adding ' + label.toLowerCase() + ' inside ' + parentName;
    } else {
        parentInfo.style.display = 'none';
        document.getElementById('addModalSubtitle').textContent = '';
    }

    document.getElementById('addCode').placeholder = codePlaceholders[level] || 'CODE';

    document.getElementById('addModal').style.display = 'flex';
    setTimeout(() => document.getElementById('addName').focus(), 50);
}

function closeAddModal() {
    document.getElementById('addModal').style.display = 'none';
}

function updateCodePreview() {
    var code = document.getElementById('addCode').value.toUpperCase();
    if (!code) { document.getElementById('addCodePreview').textContent = ''; return; }
    var preview = '';
    var ctx = addContext;
    if (ctx.level === 'chapter') preview = code;
    else if (ctx.level === 'topic') preview = (ctx.parentFullCode || '?') + '-' + code;
    else preview = '?-' + (ctx.parentFullCode || '?') + '-' + code;
    document.getElementById('addCodePreview').textContent = 'Full code: ' + preview;
}

// ===== Edit Modal =====
function openEditModal(id, name, code, level, subjectId, weightage) {
    var label = levelLabels[level] || level;
    document.getElementById('editModalTitle').textContent = 'Edit ' + label;
    document.getElementById('editModalSub').textContent   = name;
    document.getElementById('editForm').action = '/admin/curriculum/' + id;
    document.getElementById('editSubjectId').value  = subjectId;
    document.getElementById('editName').value        = name;
    document.getElementById('editCode').value        = code;
    document.getElementById('editWeightage').value   = (weightage !== null && weightage !== undefined) ? weightage : '';
    document.getElementById('editModal').style.display = 'flex';
    setTimeout(() => document.getElementById('editName').focus(), 50);
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// ===== Delete =====
function confirmDelete(id, name, level, subjectId) {
    var label = levelLabels[level] || level;
    var msg = 'Delete ' + label + ' "' + name + '"?';
    if (level === 'chapter') msg += '\n\nThis will also delete all topics and subtopics inside it.';
    else if (level === 'topic') msg += '\n\nThis will also delete all subtopics inside it.';
    if (!confirm(msg)) return;
    var form = document.getElementById('deleteForm');
    form.action = '/admin/curriculum/' + id;
    form.submit();
}

// Close modals on backdrop click
document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// Close modals on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeAddModal(); closeEditModal(); }
});
</script>
@endsection
