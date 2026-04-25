<?php

namespace App\Http\Controllers\SubAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    // Subject code → subject_id mapping (matches DB subjects table)
    private const SUBJECT_MAP = ['P' => 1, 'C' => 2, 'B' => 5, 'Z' => 4];

    // Excel row indices
    private const ROW_SUBJECTS   = 1;
    private const ROW_TOPIC_CODES = 2;
    private const ROW_NEGATIVE   = 4;
    private const ROW_MARKS      = 5;
    private const ROW_ANSWER_KEY = 6;
    private const FIRST_DATA_ROW = 7;

    // ──────────────────────────────────────────────────────────
    // STEP 1 — Select Test
    // ──────────────────────────────────────────────────────────
    public function selectTest(Request $request)
    {
        $instituteId = Auth::user()->institute_id;
        $search      = trim($request->query('search', ''));
        $courseId    = $request->query('course_id', '');
        $testType    = $request->query('test_type', '');

        $courses = DB::table('courses')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'exam_type', 'target_year']);

        $query = DB::table('tests')
            ->where('tests.institute_id', $instituteId)
            ->whereIn('tests.status', ['scheduled', 'conducted', 'blueprint_ready'])
            ->orderBy('tests.test_date', 'desc');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('tests.test_code', 'like', "%{$search}%")
                  ->orWhere('tests.name', 'like', "%{$search}%");
            });
        }
        if ($testType !== '') {
            $query->where('tests.test_type', $testType);
        }

        $tests = $query->get();

        $tests = $tests->map(function ($test) use ($courseId) {
            $batchIds = DB::table('test_batches')->where('test_id', $test->id)->pluck('batch_id');
            $test->batch_count   = $batchIds->count();
            $test->student_count = $batchIds->isNotEmpty()
                ? DB::table('students')->whereIn('batch_id', $batchIds)->where('is_active', true)->count()
                : 0;
            $firstBatch = $batchIds->isNotEmpty()
                ? DB::table('batches')->leftJoin('courses', 'batches.course_id', '=', 'courses.id')
                    ->where('batches.id', $batchIds->first())
                    ->first(['courses.exam_type', 'courses.target_year'])
                : null;
            $test->course_name = $firstBatch ? ($firstBatch->exam_type . '-' . $firstBatch->target_year) : null;
            return $test;
        });

        if ($courseId !== '') {
            $tests = $tests->filter(function ($test) use ($courseId) {
                $batchIds = DB::table('test_batches')->where('test_id', $test->id)->pluck('batch_id');
                return DB::table('batches')->whereIn('id', $batchIds)->where('course_id', $courseId)->exists();
            });
        }

        return view('sub-admin.results.step1', compact('tests', 'courses', 'search', 'courseId', 'testType'));
    }

    // ──────────────────────────────────────────────────────────
    // STEP 1 → STEP 2 redirect
    // ──────────────────────────────────────────────────────────
    public function goToUpload(Request $request)
    {
        $request->validate(['test_id' => 'required|exists:tests,id']);
        return redirect()->route('sub-admin.results.upload.file', $request->test_id);
    }

    // ──────────────────────────────────────────────────────────
    // STEP 2 — Show upload form
    // ──────────────────────────────────────────────────────────
    public function showUploadForm(Request $request, $testId)
    {
        $instituteId = Auth::user()->institute_id;
        $test = DB::table('tests')->where('id', $testId)->where('institute_id', $instituteId)->first();
        if (!$test) abort(404);

        $test = $this->attachTestMeta($test, $testId, $instituteId);
        $validation = session('omr_validation_' . $testId);

        return view('sub-admin.results.step2', compact('test', 'validation'));
    }

    // ──────────────────────────────────────────────────────────
    // STEP 2 — Handle file upload + validate
    // ──────────────────────────────────────────────────────────
    public function processUpload(Request $request, $testId)
    {
        $request->validate(['omr_file' => 'required|file|mimes:xlsx,xls,csv|max:10240']);

        $instituteId = Auth::user()->institute_id;
        $test = DB::table('tests')->where('id', $testId)->where('institute_id', $instituteId)->first();
        if (!$test) abort(404);

        $file = $request->file('omr_file');

        try {
            $rollNumbers = $this->parseExcelRolls($file);
        } catch (\Exception $e) {
            return back()->withErrors(['omr_file' => 'Could not parse file: ' . $e->getMessage()]);
        }

        if (empty($rollNumbers)) {
            return back()->withErrors(['omr_file' => 'No roll numbers found. Check the file format.']);
        }

        $batchIds = DB::table('test_batches')->where('test_id', $testId)->pluck('batch_id');
        $enrolledStudents = DB::table('students')
            ->whereIn('batch_id', $batchIds)
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->get(['id', 'roll_number', 'name', 'batch_id']);

        $submittedNorm = array_map(fn($r) => strtolower(trim((string)$r)), $rollNumbers);
        $matched = [];
        $unmatched = [];

        foreach ($rollNumbers as $roll) {
            $norm = strtolower(trim((string)$roll));
            $student = $enrolledStudents->first(fn($s) => strtolower($s->roll_number) === $norm);
            if ($student) {
                $matched[] = $roll;
            } else {
                $bestDist = PHP_INT_MAX;
                $bestStudent = null;
                foreach ($enrolledStudents as $s) {
                    $dist = levenshtein(strtolower($s->roll_number), $norm);
                    if ($dist < $bestDist) { $bestDist = $dist; $bestStudent = $s; }
                }
                $unmatched[] = [
                    'roll'      => $roll,
                    'suggested' => ($bestDist <= 3 && $bestStudent) ? [
                        'id' => $bestStudent->id, 'roll_number' => $bestStudent->roll_number, 'name' => $bestStudent->name,
                    ] : null,
                    'distance' => $bestDist,
                ];
            }
        }

        $absentStudents = $enrolledStudents->filter(
            fn($s) => !in_array(strtolower($s->roll_number), $submittedNorm)
        );

        $batchBreakdown = [];
        $batches = DB::table('batches')->whereIn('id', $batchIds)->get(['id', 'name', 'code']);
        foreach ($batches as $batch) {
            $bs = $enrolledStudents->where('batch_id', $batch->id);
            $bt = $bs->count();
            $bm = $bs->filter(fn($s) => in_array(strtolower($s->roll_number), $submittedNorm))->count();
            $batchBreakdown[] = ['name' => $batch->name, 'code' => $batch->code ?? '', 'total' => $bt, 'matched' => $bm, 'absent' => $bt - $bm];
        }

        $storedPath = $file->store('omr-uploads', 'local');
        $uploadBatchId = DB::table('omr_upload_batches')->insertGetId([
            'institute_id'   => $instituteId,
            'test_id'        => $testId,
            'uploaded_by'    => Auth::id(),
            'file_path'      => $storedPath,
            'file_name'      => $file->getClientOriginalName(),
            'file_size'      => $file->getSize(),
            'status'         => 'validating',
            'total_rows'     => count($rollNumbers),
            'matched_rows'   => count($matched),
            'unmatched_rows' => count($unmatched),
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        session(['omr_validation_' . $testId => [
            'upload_batch_id' => $uploadBatchId,
            'file_name'       => $file->getClientOriginalName(),
            'file_size'       => $file->getSize(),
            'total'           => count($rollNumbers),
            'matched'         => count($matched),
            'unmatched'       => $unmatched,
            'absent_count'    => $absentStudents->count(),
            'batch_breakdown' => $batchBreakdown,
            'match_rate'      => count($rollNumbers) > 0 ? round(count($matched) / count($rollNumbers) * 100) : 0,
        ]]);

        return redirect()->route('sub-admin.results.upload.file', $testId);
    }

    // ──────────────────────────────────────────────────────────
    // STEP 3 — Show manual mapping
    // ──────────────────────────────────────────────────────────
    public function showMapping(Request $request, $testId)
    {
        $instituteId = Auth::user()->institute_id;
        $test = DB::table('tests')->where('id', $testId)->where('institute_id', $instituteId)->first();
        if (!$test) abort(404);

        $validation = session('omr_validation_' . $testId);
        if (!$validation) return redirect()->route('sub-admin.results.upload.file', $testId);

        // No unmatched → skip to Step 4
        if (empty($validation['unmatched'])) {
            return redirect()->route('sub-admin.results.upload.analyze', $testId);
        }

        $test = $this->attachTestMeta($test, $testId, $instituteId);

        $batchIds = DB::table('test_batches')->where('test_id', $testId)->pluck('batch_id');
        $students = DB::table('students')
            ->whereIn('batch_id', $batchIds)
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('roll_number')
            ->get(['id', 'roll_number', 'name']);

        $resolved = session('omr_resolved_' . $testId, []);

        return view('sub-admin.results.step3', compact('test', 'validation', 'students', 'resolved'));
    }

    // ──────────────────────────────────────────────────────────
    // STEP 3 — Save mapping decisions
    // ──────────────────────────────────────────────────────────
    public function saveMapping(Request $request, $testId)
    {
        $instituteId = Auth::user()->institute_id;
        $test = DB::table('tests')->where('id', $testId)->where('institute_id', $instituteId)->first();
        if (!$test) abort(404);

        $validation = session('omr_validation_' . $testId);
        if (!$validation) return redirect()->route('sub-admin.results.upload.file', $testId);

        $decisions = $request->input('decision', []);
        $mappedTo  = $request->input('mapped_to', []);

        $resolved = [];
        foreach ($validation['unmatched'] as $u) {
            $roll   = $u['roll'];
            $action = $decisions[$roll] ?? 'skip';
            $sid    = ($action === 'map' && !empty($mappedTo[$roll])) ? (int)$mappedTo[$roll] : null;
            $resolved[$roll] = ['action' => $action, 'student_id' => $sid];
        }

        session(['omr_resolved_' . $testId => $resolved]);

        return redirect()->route('sub-admin.results.upload.analyze', $testId);
    }

    // ──────────────────────────────────────────────────────────
    // STEP 4 — Show analysis preview
    // ──────────────────────────────────────────────────────────
    public function showAnalyze(Request $request, $testId)
    {
        $instituteId = Auth::user()->institute_id;
        $test = DB::table('tests')->where('id', $testId)->where('institute_id', $instituteId)->first();
        if (!$test) abort(404);

        $validation = session('omr_validation_' . $testId);
        if (!$validation) return redirect()->route('sub-admin.results.upload.file', $testId);

        $test     = $this->attachTestMeta($test, $testId, $instituteId);
        $resolved = session('omr_resolved_' . $testId, []);

        $mappedCount  = collect($resolved)->filter(fn($r) => $r['action'] === 'map')->count();
        $skippedCount = collect($resolved)->filter(fn($r) => $r['action'] === 'skip')->count();
        $processCount = $validation['matched'] + $mappedCount;
        $absentCount  = $validation['absent_count'] + $skippedCount + (count($validation['unmatched']) - $mappedCount - $skippedCount);

        return view('sub-admin.results.step4', compact('test', 'validation', 'resolved', 'processCount', 'absentCount'));
    }

    // ──────────────────────────────────────────────────────────
    // STEP 4 — Run the scoring engine
    // ──────────────────────────────────────────────────────────
    public function runAnalysis(Request $request, $testId)
    {
        set_time_limit(0);
        ini_set('memory_limit', '256M');

        $instituteId = Auth::user()->institute_id;
        $test = DB::table('tests')->where('id', $testId)->where('institute_id', $instituteId)->first();
        if (!$test) abort(404);

        $validation = session('omr_validation_' . $testId);
        if (!$validation) return redirect()->route('sub-admin.results.upload.file', $testId);

        $resolved = session('omr_resolved_' . $testId, []);

        // Load the stored file
        $uploadBatch = DB::table('omr_upload_batches')->find($validation['upload_batch_id']);
        $filePath    = Storage::disk('local')->path($uploadBatch->file_path);

        // Parse full Excel
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $rows = $reader->load($filePath)->getActiveSheet()->toArray(null, true, true, false);

        // Metadata rows
        $subjectRow   = $rows[self::ROW_SUBJECTS];
        $topicRow     = $rows[self::ROW_TOPIC_CODES];
        $negativeRow  = $rows[self::ROW_NEGATIVE];
        $marksRow     = $rows[self::ROW_MARKS];
        $answerKeyRow = $rows[self::ROW_ANSWER_KEY];

        $numCols = count($subjectRow);

        // Build per-question metadata (1-indexed, column 0 = roll number)
        $questions = [];
        for ($q = 1; $q < $numCols; $q++) {
            $subjectCode = strtoupper(trim((string)($subjectRow[$q] ?? '')));
            $topicCode   = trim((string)($topicRow[$q] ?? ''));
            $questions[$q] = [
                'subject_code'   => $subjectCode,
                'subject_id'     => self::SUBJECT_MAP[$subjectCode] ?? null,
                'topic_code'     => $topicCode,
                'correct_answer' => trim((string)($answerKeyRow[$q] ?? '')),
                'pos_marks'      => (float)($marksRow[$q] ?? 4),
                'neg_marks'      => (float)($negativeRow[$q] ?? -1),
            ];
        }

        // Ensure curriculum nodes exist for every topic code
        $nodeIds = [];
        foreach ($questions as $qdata) {
            $code = $qdata['topic_code'];
            if (!$code || isset($nodeIds[$code])) continue;
            $node = DB::table('curriculum_nodes')
                ->where('institute_id', $instituteId)->where('full_code', $code)->first(['id']);
            $nodeIds[$code] = $node ? $node->id : DB::table('curriculum_nodes')->insertGetId([
                'institute_id'   => $instituteId,
                'subject_id'     => $qdata['subject_id'] ?? 1,
                'parent_id'      => null,
                'level'          => 'chapter',
                'name'           => $code,
                'code'           => $code,
                'full_code'      => $code,
                'display_order'  => 0,
                'is_active'      => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        // Create test_questions if they don't exist yet
        if (DB::table('test_questions')->where('test_id', $testId)->doesntExist()) {
            $qRows = [];
            foreach ($questions as $qNum => $qdata) {
                if (!$qdata['topic_code']) continue;
                $qRows[] = [
                    'test_id'            => $testId,
                    'question_number'    => $qNum,
                    'subject_id'         => $qdata['subject_id'] ?? 1,
                    'curriculum_node_id' => $nodeIds[$qdata['topic_code']] ?? 1,
                    'topic_code'         => $qdata['topic_code'],
                    'correct_answer'     => $qdata['correct_answer'],
                    'correct_marks'      => $qdata['pos_marks'],
                    'incorrect_marks'    => $qdata['neg_marks'],
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ];
            }
            foreach (array_chunk($qRows, 100) as $chunk) {
                DB::table('test_questions')->insert($chunk);
            }
        }

        // Load test_questions keyed by question_number
        $testQuestions = DB::table('test_questions')
            ->where('test_id', $testId)
            ->get(['id', 'question_number', 'subject_id', 'curriculum_node_id', 'correct_answer', 'correct_marks', 'incorrect_marks'])
            ->keyBy('question_number');

        // Load students keyed by normalized roll
        $batchIds = DB::table('test_batches')->where('test_id', $testId)->pluck('batch_id');
        $students = DB::table('students')
            ->whereIn('batch_id', $batchIds)
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->get(['id', 'roll_number', 'batch_id'])
            ->keyBy(fn($s) => strtolower(trim($s->roll_number)));

        // Resolved manual mappings: roll → student_id
        $resolvedMap = [];
        foreach ($resolved as $roll => $dec) {
            if ($dec['action'] === 'map' && $dec['student_id']) {
                $resolvedMap[strtolower(trim($roll))] = $dec['student_id'];
            }
        }

        // Clear previous results for this test (safe re-run)
        DB::table('student_responses')->where('test_id', $testId)->delete();
        DB::table('test_results_cache')->where('test_id', $testId)->delete();

        $studentResults = []; // student_id → result accumulators
        $masteryBuffer  = []; // "student_id|node_id" → mastery accumulators
        $responseBuffer = [];
        $now = now();

        foreach ($rows as $rowIdx => $row) {
            if ($rowIdx < self::FIRST_DATA_ROW) continue;
            $rollRaw = trim((string)($row[0] ?? ''));
            if (!is_numeric($rollRaw)) continue;
            $rollNorm = strtolower($rollRaw);

            // Find student
            $student = $students[$rollNorm] ?? null;
            if (!$student && isset($resolvedMap[$rollNorm])) {
                $sid     = $resolvedMap[$rollNorm];
                $student = DB::table('students')->where('id', $sid)->first(['id', 'roll_number', 'batch_id']);
            }
            if (!$student) continue;

            $studentId = $student->id;
            $batchId   = $student->batch_id;

            $result = [
                'total_marks'       => 0.0,
                'total_correct'     => 0,
                'total_incorrect'   => 0,
                'total_unattempted' => 0,
                'batch_id'          => $batchId,
                'subject_scores'    => [],
            ];

            for ($q = 1; $q < $numCols; $q++) {
                $tq = $testQuestions[$q] ?? null;
                if (!$tq) continue;

                $rawAns   = trim((string)($row[$q] ?? ''));
                $isX      = (strtolower($rawAns) === 'x' || $rawAns === '');
                $isStar   = ($rawAns === '*');
                $isAttempted = !$isX;

                $submitted  = $isAttempted ? $rawAns : null;
                $correctAns = trim((string)$tq->correct_answer);
                $posM       = (float)($tq->correct_marks ?? 4);
                $negM       = (float)($tq->incorrect_marks ?? -1);
                $invalidM   = (float)($test->invalid_marks ?? -1);

                $isCorrect    = null;
                $marksAwarded = 0.0;

                if ($isStar) {
                    $marksAwarded = $invalidM;
                    $isCorrect    = false;
                    $result['total_incorrect']++;
                } elseif ($isX) {
                    $marksAwarded = 0.0;
                    $result['total_unattempted']++;
                } elseif ($rawAns === $correctAns) {
                    $isCorrect    = true;
                    $marksAwarded = $posM;
                    $result['total_correct']++;
                } else {
                    $isCorrect    = false;
                    $marksAwarded = $negM;
                    $result['total_incorrect']++;
                }

                $result['total_marks'] += $marksAwarded;

                $sc = $questions[$q]['subject_code'] ?? 'X';
                if (!isset($result['subject_scores'][$sc])) {
                    $result['subject_scores'][$sc] = ['marks' => 0, 'correct' => 0, 'incorrect' => 0, 'unattempted' => 0];
                }
                $result['subject_scores'][$sc]['marks'] += $marksAwarded;
                if ($isCorrect === true)  $result['subject_scores'][$sc]['correct']++;
                if ($isCorrect === false) $result['subject_scores'][$sc]['incorrect']++;
                if (!$isAttempted)        $result['subject_scores'][$sc]['unattempted']++;

                $responseBuffer[] = [
                    'test_id'            => $testId,
                    'student_id'         => $studentId,
                    'test_question_id'   => $tq->id,
                    'submitted_answer'   => $submitted,
                    'is_correct'         => $isCorrect,
                    'marks_awarded'      => $marksAwarded,
                    'time_taken_seconds' => null,
                    'created_at'         => $now,
                    'updated_at'         => $now,
                ];

                if ($tq->curriculum_node_id) {
                    $mKey = $studentId . '|' . $tq->curriculum_node_id;
                    if (!isset($masteryBuffer[$mKey])) {
                        $masteryBuffer[$mKey] = [
                            'student_id'         => $studentId,
                            'curriculum_node_id' => $tq->curriculum_node_id,
                            'subject_id'         => $tq->subject_id,
                            'attempts'           => 0, 'correct' => 0,
                            'marks_earned'       => 0.0, 'marks_possible' => 0.0,
                        ];
                    }
                    if ($isAttempted) $masteryBuffer[$mKey]['attempts']++;
                    if ($isCorrect === true) $masteryBuffer[$mKey]['correct']++;
                    $masteryBuffer[$mKey]['marks_earned']   += max(0, $marksAwarded);
                    $masteryBuffer[$mKey]['marks_possible'] += $posM;
                }
            }

            $studentResults[$studentId] = $result;

            if (count($responseBuffer) >= 500) {
                DB::table('student_responses')->insert($responseBuffer);
                $responseBuffer = [];
            }
        }

        if (!empty($responseBuffer)) {
            DB::table('student_responses')->insert($responseBuffer);
        }

        // Insert test_results_cache
        $cacheRows = [];
        foreach ($studentResults as $studentId => $result) {
            $cacheRows[] = [
                'test_id'           => $testId,
                'student_id'        => $studentId,
                'batch_id'          => $result['batch_id'],
                'total_marks'       => round($result['total_marks'], 2),
                'total_correct'     => $result['total_correct'],
                'total_incorrect'   => $result['total_incorrect'],
                'total_unattempted' => $result['total_unattempted'],
                'rank_in_batch'     => null,
                'rank_in_course'    => null,
                'percentile'        => null,
                'subject_scores'    => json_encode($result['subject_scores']),
                'created_at'        => $now,
                'updated_at'        => $now,
            ];
        }
        foreach (array_chunk($cacheRows, 100) as $chunk) {
            DB::table('test_results_cache')->insert($chunk);
        }

        // Calculate ranks + percentile (bulk, single transaction)
        $allResults    = DB::table('test_results_cache')
            ->where('test_id', $testId)->orderByDesc('total_marks')->get(['id', 'total_marks']);
        $totalStudents = $allResults->count();
        DB::transaction(function () use ($allResults, $totalStudents, $now) {
            foreach ($allResults as $rank => $r) {
                $rankNum    = $rank + 1;
                $percentile = $totalStudents > 1
                    ? round(($totalStudents - $rankNum) / ($totalStudents - 1) * 100, 2)
                    : 100.00;
                DB::table('test_results_cache')->where('id', $r->id)->update([
                    'rank_in_batch'  => $rankNum,
                    'rank_in_course' => $rankNum,
                    'percentile'     => $percentile,
                    'updated_at'     => $now,
                ]);
            }
        });

        // Update student_subtopic_mastery — pre-load all existing records in ONE query
        if (!empty($masteryBuffer)) {
            $mStudentIds = array_unique(array_column(array_values($masteryBuffer), 'student_id'));
            $mNodeIds    = array_unique(array_column(array_values($masteryBuffer), 'curriculum_node_id'));

            $existingMastery = DB::table('student_subtopic_mastery')
                ->whereIn('student_id', $mStudentIds)
                ->whereIn('curriculum_node_id', $mNodeIds)
                ->get()
                ->keyBy(fn($r) => $r->student_id . '|' . $r->curriculum_node_id);

            $masteryInserts = [];
            $masteryUpdates = []; // id → data

            foreach ($masteryBuffer as $key => $mu) {
                $existing    = $existingMastery[$key] ?? null;
                $newAttempts = ($existing->total_questions_attempted ?? 0) + $mu['attempts'];
                $newCorrect  = ($existing->total_questions_correct ?? 0) + $mu['correct'];
                $newEarned   = ($existing->total_marks_earned ?? 0) + $mu['marks_earned'];
                $newPossible = ($existing->total_marks_possible ?? 0) + $mu['marks_possible'];
                $mastery     = $newAttempts > 0 ? round($newCorrect / $newAttempts * 100, 2) : 0;
                $accuracy    = $newPossible > 0 ? round($newEarned / $newPossible * 100, 2) : 0;

                $data = [
                    'total_questions_attempted' => $newAttempts,
                    'total_questions_correct'   => $newCorrect,
                    'total_marks_earned'        => $newEarned,
                    'total_marks_possible'      => $newPossible,
                    'mastery_percentage'        => $mastery,
                    'accuracy_percentage'       => $accuracy,
                    'last_test_score'           => round($mu['marks_earned'], 2),
                    'last_updated_at'           => $now,
                    'updated_at'                => $now,
                ];

                if ($existing) {
                    $masteryUpdates[$existing->id] = $data;
                } else {
                    $masteryInserts[] = array_merge($data, [
                        'institute_id'       => $instituteId,
                        'student_id'         => $mu['student_id'],
                        'curriculum_node_id' => $mu['curriculum_node_id'],
                        'subject_id'         => $mu['subject_id'],
                        'created_at'         => $now,
                    ]);
                }
            }

            // Bulk insert new mastery records
            foreach (array_chunk($masteryInserts, 100) as $chunk) {
                DB::table('student_subtopic_mastery')->insert($chunk);
            }

            // Bulk update existing mastery records in a single transaction
            DB::transaction(function () use ($masteryUpdates) {
                foreach ($masteryUpdates as $id => $data) {
                    DB::table('student_subtopic_mastery')->where('id', $id)->update($data);
                }
            });
        }

        // Finalize test + upload batch
        DB::table('tests')->where('id', $testId)->update([
            'status'                => 'analyzed',
            'responses_uploaded_at' => $now,
            'analyzed_at'           => $now,
            'updated_at'            => $now,
        ]);
        DB::table('omr_upload_batches')->where('id', $validation['upload_batch_id'])->update([
            'status'       => 'completed',
            'completed_at' => $now,
            'updated_at'   => $now,
        ]);

        session()->forget(['omr_validation_' . $testId, 'omr_resolved_' . $testId]);

        return redirect()->route('sub-admin.tests.index')
            ->with('success', 'Analysis complete! ' . $totalStudents . ' students processed for "' . $test->name . '".');
    }

    // ──────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────
    private function attachTestMeta($test, $testId, $instituteId)
    {
        $batchIds = DB::table('test_batches')->where('test_id', $testId)->pluck('batch_id');
        $test->batch_count   = $batchIds->count();
        $test->student_count = $batchIds->isNotEmpty()
            ? DB::table('students')->whereIn('batch_id', $batchIds)->where('is_active', true)->count()
            : 0;
        if ($batchIds->isNotEmpty()) {
            $fb = DB::table('batches')->leftJoin('courses', 'batches.course_id', '=', 'courses.id')
                ->where('batches.id', $batchIds->first())->first(['courses.exam_type', 'courses.target_year']);
            $test->course_label = $fb ? ($fb->exam_type . '-' . $fb->target_year) : null;
        } else {
            $test->course_label = null;
        }
        return $test;
    }

    private function parseExcelRolls($file): array
    {
        $ext  = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if ($ext === 'csv') {
            $rolls  = [];
            $handle = fopen($path, 'r');
            $first  = true;
            while (($row = fgetcsv($handle)) !== false) {
                if ($first) { $first = false; continue; }
                $roll = trim($row[0] ?? '');
                if (is_numeric($roll)) $rolls[] = $roll;
            }
            fclose($handle);
            return $rolls;
        }

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $rows  = $reader->load($path)->getActiveSheet()->toArray(null, true, true, false);
        $rolls = [];
        foreach ($rows as $i => $row) {
            if ($i < self::FIRST_DATA_ROW) continue;
            $roll = trim((string)($row[0] ?? ''));
            if (is_numeric($roll)) $rolls[] = $roll;
        }
        return $rolls;
    }
}
