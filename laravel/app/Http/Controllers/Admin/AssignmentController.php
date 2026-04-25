<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    private function instituteId(): int
    {
        return Auth::user()->institute_id;
    }

    public function index()
    {
        $instituteId = $this->instituteId();

        // All teachers for this institute
        $teachers = DB::table('users')
            ->where('institute_id', $instituteId)
            ->where('role', 'teacher')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // All active batches with course info
        $batches = DB::table('batches')
            ->join('courses', 'courses.id', '=', 'batches.course_id')
            ->where('batches.institute_id', $instituteId)
            ->where('batches.is_active', true)
            ->select('batches.*', 'courses.name as course_name', 'courses.exam_type as course_exam_type')
            ->orderBy('courses.exam_type')
            ->orderBy('batches.name')
            ->get();

        // All current assignments — lookup as "userId:batchId"
        $teacherIds = $teachers->pluck('id');
        $batchIds   = $batches->pluck('id');

        $assigned = DB::table('user_batch_assignments')
            ->whereIn('user_id', $teacherIds)
            ->whereIn('batch_id', $batchIds)
            ->get()
            ->mapWithKeys(fn($r) => ["{$r->user_id}:{$r->batch_id}" => true]);

        // First subject per teacher (for badge + compatibility)
        $teacherSubjects = DB::table('user_subject_assignments as usa')
            ->join('subjects', 'subjects.id', '=', 'usa.subject_id')
            ->whereIn('usa.user_id', $teacherIds)
            ->select('usa.user_id', 'subjects.name as subject_name', 'subjects.code as subject_code', 'subjects.exam_type as subject_exam_type')
            ->orderBy('subjects.display_order')
            ->get()
            ->groupBy('user_id')
            ->map(fn($rows) => $rows->first());

        // Stats
        $assignmentCount = $assigned->count();
        $fullyAssigned   = 0;
        foreach ($teachers as $t) {
            $appCount  = 0;
            $doneCount = 0;
            foreach ($batches as $b) {
                if (self::isApplicable($teacherSubjects[$t->id]->subject_exam_type ?? 'BOTH', $b->course_exam_type)) {
                    $appCount++;
                    if ($assigned->has("{$t->id}:{$b->id}")) $doneCount++;
                }
            }
            if ($appCount > 0 && $appCount === $doneCount) $fullyAssigned++;
        }

        return view('admin.assignments.index', compact(
            'teachers', 'batches', 'assigned', 'teacherSubjects',
            'assignmentCount', 'fullyAssigned'
        ));
    }

    public function toggle(Request $request)
    {
        $data = $request->validate([
            'user_id'  => 'required|integer|exists:users,id',
            'batch_id' => 'required|integer|exists:batches,id',
        ]);

        $instituteId = $this->instituteId();

        // Verify user belongs to this institute
        $userOk  = DB::table('users')->where('id', $data['user_id'])->where('institute_id', $instituteId)->exists();
        $batchOk = DB::table('batches')->where('id', $data['batch_id'])->where('institute_id', $instituteId)->exists();

        if (!$userOk || !$batchOk) {
            return response()->json(['error' => 'Not found'], 403);
        }

        $exists = DB::table('user_batch_assignments')
            ->where('user_id', $data['user_id'])
            ->where('batch_id', $data['batch_id'])
            ->exists();

        if ($exists) {
            DB::table('user_batch_assignments')
                ->where('user_id', $data['user_id'])
                ->where('batch_id', $data['batch_id'])
                ->delete();
            return response()->json(['assigned' => false]);
        } else {
            DB::table('user_batch_assignments')->insert([
                'user_id'    => $data['user_id'],
                'batch_id'   => $data['batch_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return response()->json(['assigned' => true]);
        }
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'scope'   => 'required|in:all_applicable,all',
        ]);

        $instituteId = $this->instituteId();

        $user = DB::table('users')->where('id', $data['user_id'])->where('institute_id', $instituteId)->first();
        if (!$user) abort(403);

        $batches = DB::table('batches')
            ->join('courses', 'courses.id', '=', 'batches.course_id')
            ->where('batches.institute_id', $instituteId)
            ->where('batches.is_active', true)
            ->select('batches.id', 'courses.exam_type as course_exam_type')
            ->get();

        $subj = DB::table('user_subject_assignments as usa')
            ->join('subjects', 'subjects.id', '=', 'usa.subject_id')
            ->where('usa.user_id', $data['user_id'])
            ->orderBy('subjects.display_order')
            ->value('subjects.exam_type');

        foreach ($batches as $batch) {
            $applicable = $data['scope'] === 'all' || self::isApplicable($subj, $batch->course_exam_type);
            if (!$applicable) continue;

            DB::table('user_batch_assignments')->insertOrIgnore([
                'user_id'    => $data['user_id'],
                'batch_id'   => $batch->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.assignments')->with('success', 'Bulk assignment complete.');
    }

    public static function isApplicable(?string $subjectExamType, string $courseExamType): bool
    {
        if ($subjectExamType === null || $subjectExamType === 'BOTH') return true;
        if ($courseExamType === 'OTHER') return true;
        if ($subjectExamType === 'NEET' && $courseExamType === 'NEET') return true;
        if ($subjectExamType === 'JEE' && in_array($courseExamType, ['JEE_MAIN', 'JEE_ADVANCED'])) return true;
        return false;
    }
}
