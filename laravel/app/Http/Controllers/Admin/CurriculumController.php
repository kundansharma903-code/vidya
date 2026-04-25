<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CurriculumController extends Controller
{
    private function instituteId(): int
    {
        return Auth::user()->institute_id;
    }

    public function index(Request $request)
    {
        $instituteId = $this->instituteId();

        $subjects = DB::table('subjects')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderBy('name')
            ->get();

        $activeSubjectId = (int) ($request->subject_id ?? ($subjects->first()->id ?? 0));
        $activeSubject   = $subjects->firstWhere('id', $activeSubjectId);

        $chapters         = collect();
        $topicsByChapter  = collect();
        $subtopicsByTopic = collect();

        if ($activeSubjectId) {
            $nodes = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)
                ->where('subject_id', $activeSubjectId)
                ->where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get();

            $chapters         = $nodes->where('level', 'chapter')->values();
            $topicsByChapter  = $nodes->where('level', 'topic')->groupBy('parent_id');
            $subtopicsByTopic = $nodes->where('level', 'subtopic')->groupBy('parent_id');
        }

        $stats = DB::table('curriculum_nodes')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->selectRaw('SUM(CASE WHEN level = "subtopic" THEN 1 ELSE 0 END) as subtopic_count')
            ->first();

        return view('admin.curriculum.index', compact(
            'subjects', 'activeSubjectId', 'activeSubject',
            'chapters', 'topicsByChapter', 'subtopicsByTopic', 'stats'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'parent_id'  => 'nullable|integer|exists:curriculum_nodes,id',
            'level'      => 'required|in:chapter,topic,subtopic',
            'name'       => 'required|string|max:150',
            'code'       => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9]+$/'],
            'weightage'  => 'nullable|numeric|min:0|max:100',
        ]);

        $instituteId = $this->instituteId();
        $code        = strtoupper($data['code']);
        $fullCode    = $this->buildFullCode($data['level'], $code, $data['parent_id'] ?? null, $data['subject_id']);

        if (DB::table('curriculum_nodes')->where('institute_id', $instituteId)->where('full_code', $fullCode)->exists()) {
            return back()->withErrors(['code' => "Code '{$fullCode}' already exists in this institute."])->withInput();
        }

        DB::table('curriculum_nodes')->insert([
            'institute_id'  => $instituteId,
            'subject_id'    => $data['subject_id'],
            'parent_id'     => $data['parent_id'] ?? null,
            'level'         => $data['level'],
            'name'          => $data['name'],
            'code'          => $code,
            'full_code'     => $fullCode,
            'display_order' => 0,
            'weightage'     => $data['weightage'] ?? null,
            'is_active'     => true,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return redirect()->route('admin.curriculum', ['subject_id' => $data['subject_id']])
            ->with('success', ucfirst($data['level']) . ' added successfully.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:150',
            'code'      => ['required', 'string', 'max:20', 'regex:/^[A-Za-z0-9]+$/'],
            'weightage' => 'nullable|numeric|min:0|max:100',
        ]);

        $instituteId = $this->instituteId();
        $node = DB::table('curriculum_nodes')
            ->where('id', $id)->where('institute_id', $instituteId)->first();

        if (!$node) abort(404);

        $code     = strtoupper($data['code']);
        $fullCode = $this->buildFullCode($node->level, $code, $node->parent_id, $node->subject_id);

        if (DB::table('curriculum_nodes')->where('institute_id', $instituteId)->where('full_code', $fullCode)->where('id', '!=', $id)->exists()) {
            return back()->withErrors(['code' => "Code '{$fullCode}' already exists."])->withInput();
        }

        DB::table('curriculum_nodes')->where('id', $id)->update([
            'name'       => $data['name'],
            'code'       => $code,
            'full_code'  => $fullCode,
            'weightage'  => $data['weightage'] ?? null,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.curriculum', ['subject_id' => $node->subject_id])
            ->with('success', 'Updated successfully.');
    }

    public function destroy(int $id)
    {
        $instituteId = $this->instituteId();
        $node = DB::table('curriculum_nodes')
            ->where('id', $id)->where('institute_id', $instituteId)->first();

        if (!$node) abort(404);

        DB::table('curriculum_nodes')->where('id', $id)->delete();

        return redirect()->route('admin.curriculum', ['subject_id' => $node->subject_id])
            ->with('success', ucfirst($node->level) . ' deleted.');
    }

    private function buildFullCode(string $level, string $code, ?int $parentId, int $subjectId): string
    {
        if ($level === 'chapter') {
            return $code;
        }

        $parent = DB::table('curriculum_nodes')->where('id', $parentId)->first();

        if ($level === 'topic') {
            return $parent->full_code . '-' . $code;
        }

        // subtopic: SubjectCode-ChapterCode-TopicCode-SubtopicCode
        $subject = DB::table('subjects')->where('id', $subjectId)->first();
        return $subject->code . '-' . $parent->full_code . '-' . $code;
    }
}
