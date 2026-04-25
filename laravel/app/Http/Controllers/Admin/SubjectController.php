<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    private function instituteId(): int
    {
        return Auth::user()->institute_id;
    }

    public function index(Request $request)
    {
        $instituteId = $this->instituteId();

        $query = DB::table('subjects')
            ->where('subjects.institute_id', $instituteId)
            ->leftJoin('curriculum_nodes as cn', function ($join) use ($instituteId) {
                $join->on('cn.subject_id', '=', 'subjects.id')
                     ->where('cn.institute_id', $instituteId);
            })
            ->select(
                'subjects.*',
                DB::raw("SUM(CASE WHEN cn.level = 'chapter'  THEN 1 ELSE 0 END) as chapter_count"),
                DB::raw("SUM(CASE WHEN cn.level = 'topic'    THEN 1 ELSE 0 END) as topic_count"),
                DB::raw("SUM(CASE WHEN cn.level = 'subtopic' THEN 1 ELSE 0 END) as subtopic_count")
            )
            ->groupBy('subjects.id');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where('subjects.name', 'like', $s);
        }

        if ($request->filled('exam_type')) {
            $query->where('subjects.exam_type', $request->exam_type);
        }

        $subjects = $query->orderBy('subjects.display_order')->orderBy('subjects.name')->paginate(20)->withQueryString();

        $stats = DB::table('subjects')->where('institute_id', $instituteId)->selectRaw('
            COUNT(*) as total,
            SUM(is_active) as active_count
        ')->first();

        $totalSubtopics = DB::table('curriculum_nodes')
            ->where('institute_id', $instituteId)
            ->where('level', 'subtopic')
            ->count();

        return view('admin.subjects.index', compact('subjects', 'stats', 'totalSubtopics'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'required|string|max:1',
            'exam_type'     => 'required|in:NEET,JEE,BOTH',
            'display_order' => 'required|integer|min:0',
        ]);

        $data['institute_id'] = $this->instituteId();
        $data['is_active']    = true;

        DB::table('subjects')->insert($data + ['created_at' => now(), 'updated_at' => now()]);

        return redirect()->route('admin.subjects')->with('success', 'Subject created.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'required|string|max:1',
            'exam_type'     => 'required|in:NEET,JEE,BOTH',
            'display_order' => 'required|integer|min:0',
        ]);

        DB::table('subjects')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->update($data + ['updated_at' => now()]);

        return redirect()->route('admin.subjects')->with('success', 'Subject updated.');
    }

    public function toggle(int $id)
    {
        $subject = DB::table('subjects')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->first();

        if ($subject) {
            DB::table('subjects')->where('id', $id)->update([
                'is_active'  => !$subject->is_active,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.subjects')->with('success', 'Subject status updated.');
    }

    public function destroy(int $id)
    {
        DB::table('subjects')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->delete();

        return redirect()->route('admin.subjects')->with('success', 'Subject deleted.');
    }
}
