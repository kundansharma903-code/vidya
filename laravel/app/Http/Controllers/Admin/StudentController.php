<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentController extends Controller
{
    private function instituteId(): int
    {
        return Auth::user()->institute_id;
    }

    public function index(Request $request)
    {
        $instituteId = $this->instituteId();

        $query = DB::table('students')
            ->where('students.institute_id', $instituteId)
            ->join('batches', 'batches.id', '=', 'students.batch_id')
            ->join('courses', 'courses.id', '=', 'batches.course_id')
            ->select(
                'students.*',
                'batches.name as batch_name',
                'courses.name as course_name',
                'courses.exam_type'
            );

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('students.roll_number', 'like', $s)
                  ->orWhere('students.name', 'like', $s)
                  ->orWhere('students.phone', 'like', $s);
            });
        }

        if ($request->filled('batch_id')) {
            $query->where('students.batch_id', $request->batch_id);
        }

        if ($request->filled('course_id')) {
            $query->where('batches.course_id', $request->course_id);
        }

        if ($request->filled('status')) {
            $query->where('students.is_active', $request->status === 'active' ? 1 : 0);
        }

        $students = $query->orderByDesc('students.created_at')->paginate(10)->withQueryString();

        $stats = DB::table('students')
            ->join('batches', 'batches.id', '=', 'students.batch_id')
            ->join('courses', 'courses.id', '=', 'batches.course_id')
            ->where('students.institute_id', $instituteId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(students.is_active) as active_count,
                SUM(CASE WHEN courses.exam_type IN ("NEET","BOTH") THEN 1 ELSE 0 END) as neet_count,
                SUM(CASE WHEN courses.exam_type IN ("JEE_MAIN","JEE_ADVANCED","BOTH") THEN 1 ELSE 0 END) as jee_count
            ')
            ->first();

        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $courses = DB::table('courses')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.students.index', compact('students', 'stats', 'batches', 'courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'roll_number'       => 'required|string|max:30',
            'enrollment_number' => 'nullable|string|max:30',
            'batch_id'          => 'required|integer|exists:batches,id',
            'phone'             => 'nullable|string|max:20',
            'parent_phone'      => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:150',
            'admission_date'    => 'nullable|date',
            'father_name'       => 'nullable|string|max:100',
            'mother_name'       => 'nullable|string|max:100',
            'date_of_birth'     => 'nullable|date',
            'gender'            => 'nullable|in:M,F,O',
            'medium'            => 'nullable|in:english,hindi',
            'address'           => 'nullable|string|max:500',
        ]);

        $data['institute_id'] = $this->instituteId();
        $data['is_active']    = true;

        DB::table('students')->insert($data + ['created_at' => now(), 'updated_at' => now()]);

        return redirect()->route('admin.students')->with('success', 'Student added successfully.');
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'roll_number'       => 'required|string|max:30',
            'enrollment_number' => 'nullable|string|max:30',
            'batch_id'          => 'required|integer|exists:batches,id',
            'phone'             => 'nullable|string|max:20',
            'parent_phone'      => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:150',
            'admission_date'    => 'nullable|date',
            'father_name'       => 'nullable|string|max:100',
            'mother_name'       => 'nullable|string|max:100',
            'date_of_birth'     => 'nullable|date',
            'gender'            => 'nullable|in:M,F,O',
            'medium'            => 'nullable|in:english,hindi',
            'address'           => 'nullable|string|max:500',
        ]);

        DB::table('students')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->update($data + ['updated_at' => now()]);

        return redirect()->route('admin.students')->with('success', 'Student updated.');
    }

    public function toggle(int $id)
    {
        $student = DB::table('students')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->first();

        if ($student) {
            DB::table('students')->where('id', $id)->update([
                'is_active'  => !$student->is_active,
                'updated_at' => now(),
            ]);
        }

        return redirect()->route('admin.students')->with('success', 'Student status updated.');
    }

    public function destroy(int $id)
    {
        DB::table('students')
            ->where('id', $id)
            ->where('institute_id', $this->instituteId())
            ->delete();

        return redirect()->route('admin.students')->with('success', 'Student removed.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $instituteId = $this->instituteId();
        $file        = $request->file('excel_file');

        try {
            $spreadsheet = IOFactory::load($file->getPathname());
        } catch (\Exception $e) {
            return back()->with('import_error', 'Could not read the file. Make sure it is a valid .xlsx or .csv file.');
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows  = $sheet->toArray(null, true, true, true);

        // Build batch name → id map (case-insensitive) for this institute
        $batches = DB::table('batches')
            ->where('institute_id', $instituteId)
            ->where('is_active', true)
            ->get(['id', 'name', 'course_id']);

        $batchMap = [];
        foreach ($batches as $b) {
            $batchMap[strtolower(trim($b->name))] = $b;
        }

        $imported = 0;
        $errors   = [];
        $skipped  = 0;

        foreach ($rows as $rowNum => $row) {
            // Skip header row (row 1)
            if ($rowNum === 1) continue;

            $rollNo    = trim($row['A'] ?? '');
            $name      = trim($row['B'] ?? '');
            $batchName = trim($row['C'] ?? '');

            // Skip fully empty rows
            if ($rollNo === '' && $name === '' && $batchName === '') continue;

            // Validate required columns
            if ($rollNo === '') {
                $errors[] = "Row {$rowNum}: Roll No is empty — skipped.";
                $skipped++;
                continue;
            }
            if ($name === '') {
                $errors[] = "Row {$rowNum}: Student Name is empty — skipped.";
                $skipped++;
                continue;
            }
            if ($batchName === '') {
                $errors[] = "Row {$rowNum}: Batch Name is empty — skipped.";
                $skipped++;
                continue;
            }

            // Batch lookup
            $batch = $batchMap[strtolower($batchName)] ?? null;
            if (!$batch) {
                $errors[] = "Row {$rowNum} [{$rollNo} — {$name}]: Cannot find batch \"{$batchName}\" — student not imported.";
                $skipped++;
                continue;
            }

            // Skip duplicate roll numbers within this institute
            $exists = DB::table('students')
                ->where('institute_id', $instituteId)
                ->where('roll_number', $rollNo)
                ->exists();

            if ($exists) {
                $errors[] = "Row {$rowNum} [{$rollNo} — {$name}]: Roll number already exists — skipped.";
                $skipped++;
                continue;
            }

            DB::table('students')->insert([
                'institute_id'  => $instituteId,
                'batch_id'      => $batch->id,
                'name'          => $name,
                'roll_number'   => $rollNo,
                'is_active'     => true,
                'admission_date'=> now()->toDateString(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $imported++;
        }

        $summary = "Imported {$imported} student(s) successfully.";
        if ($skipped > 0) {
            $summary .= " {$skipped} row(s) had errors (see below).";
        }

        return redirect()->route('admin.students')
            ->with('import_success', $summary)
            ->with('import_errors', $errors);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Students');

        // Header row
        $sheet->setCellValue('A1', 'Roll No');
        $sheet->setCellValue('B1', 'Student Name');
        $sheet->setCellValue('C1', 'Batch Name');

        // Style header
        $headerStyle = [
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '1a1a2e']],
            'alignment' => ['horizontal' => 'center'],
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        // Column widths
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(25);

        // Sample rows
        $samples = [
            ['A2' => '2025001', 'B2' => 'Aarav Mehta',   'C2' => 'NEET-2025-Batch-A'],
            ['A3' => '2025002', 'B3' => 'Priya Sharma',  'C3' => 'NEET-2025-Batch-A'],
            ['A4' => '2025003', 'B4' => 'Rohan Verma',   'C4' => 'JEE-2025-Batch-B'],
        ];
        foreach ($samples as $sample) {
            foreach ($sample as $cell => $val) {
                $sheet->setCellValue($cell, $val);
            }
        }

        // Freeze header row
        $sheet->freezePane('A2');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'vidya_students_import_template.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
