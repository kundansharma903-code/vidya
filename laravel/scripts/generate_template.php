<?php
/**
 * Generates Vidya_ResultUpload_Template.xlsx from the real OMR CSV.
 * - Keeps all original rows intact (structure unchanged)
 * - Inserts TOPIC_CODES row after SUBJECTS row (row 2 → new row 3)
 * - Assigns sequential dummy codes: P-001…P-045, C-001…C-045, B-001…B-045, Z-001…Z-045
 * - Styled header/metadata rows + Legend sheet
 */

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$csvPath    = 'C:/Users/hp/Downloads/OfflineMarksImport_25_04_2026 15_01_22.csv';
$outputPath = 'C:/Users/hp/Downloads/Vidya_ResultUpload_Template.xlsx';

// ── Read CSV ───────────────────────────────────────────────────────────────
echo "Reading CSV...\n";
$handle  = fopen($csvPath, 'r');
$rawRows = [];
while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
    $rawRows[] = $row;
}
fclose($handle);

$totalCols = count($rawRows[0]); // 181 (RollNo + 180 questions)
echo "Rows: " . count($rawRows) . ", Columns: $totalCols\n";

// ── Build TOPIC_CODES row from SUBJECTS row (index 1) ─────────────────────
$subjectsRow     = $rawRows[1];
$subjectPrefix   = ['p' => 'P', 'C' => 'C', 'B' => 'B', 'Z' => 'Z'];
$subjectCounters = ['p' => 0, 'C' => 0, 'B' => 0, 'Z' => 0];
$topicCodesRow   = ['TOPIC_CODES'];

for ($i = 1; $i < $totalCols; $i++) {
    $subj = trim($subjectsRow[$i] ?? '');
    if (isset($subjectCounters[$subj])) {
        $subjectCounters[$subj]++;
        $topicCodesRow[] = $subjectPrefix[$subj] . '-' . str_pad($subjectCounters[$subj], 3, '0', STR_PAD_LEFT);
    } else {
        $topicCodesRow[] = '';
    }
}

// ── Insert TOPIC_CODES after SUBJECTS (index 1 → insert at index 2) ───────
array_splice($rawRows, 2, 0, [$topicCodesRow]);
// Final row layout:
// 0 → RollNo header
// 1 → SUBJECTS
// 2 → TOPIC_CODES  ← new
// 3 → OC
// 4 → NEGATIVE
// 5 → MARKS
// 6 → Answers (answer key)
// 7+ → student data

$totalRows = count($rawRows);

// ── Helper: convert 1-based col index to cell address ────────────────────
function cellAddr(int $col, int $row): string {
    return Coordinate::stringFromColumnIndex($col) . $row;
}

// ── Helper: row range string ──────────────────────────────────────────────
function rowRange(int $row, int $lastCol): string {
    return 'A' . $row . ':' . Coordinate::stringFromColumnIndex($lastCol) . $row;
}

// ── Build Spreadsheet ─────────────────────────────────────────────────────
echo "Building spreadsheet...\n";
$spreadsheet = new Spreadsheet();
$sheet       = $spreadsheet->getActiveSheet();
$sheet->setTitle('Result Upload');

// Write data row by row
foreach ($rawRows as $rIdx => $row) {
    $excelRow = $rIdx + 1;
    foreach ($row as $cIdx => $value) {
        $sheet->getCell(cellAddr($cIdx + 1, $excelRow))->setValue($value);
    }
}

// ── Column widths ─────────────────────────────────────────────────────────
$sheet->getColumnDimension('A')->setWidth(16);
for ($c = 2; $c <= $totalCols; $c++) {
    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setWidth(7);
}

// ── Row style helper ──────────────────────────────────────────────────────
function applyRowStyle($sheet, int $row, string $bgHex, string $fgHex, bool $bold, int $lastCol): void
{
    $range = rowRange($row, $lastCol);
    $sheet->getStyle($range)->applyFromArray([
        'font' => [
            'bold'  => $bold,
            'color' => ['rgb' => $fgHex],
            'size'  => 9,
        ],
        'fill' => [
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['rgb' => $bgHex],
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
        ],
    ]);
    $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
}

// Row colours
applyRowStyle($sheet, 1, '1A2E4A', 'FFFFFF', true,  $totalCols); // Header
applyRowStyle($sheet, 2, '2E4A6A', 'B8D4F0', true,  $totalCols); // SUBJECTS
applyRowStyle($sheet, 3, '0D3B2E', '7EE8A2', true,  $totalCols); // TOPIC_CODES ← green
applyRowStyle($sheet, 4, '2A2A2A', 'A0A0A0', false, $totalCols); // OC
applyRowStyle($sheet, 5, '4A1010', 'F0A0A0', true,  $totalCols); // NEGATIVE
applyRowStyle($sheet, 6, '0F3A1A', 'A0E8A0', true,  $totalCols); // MARKS
applyRowStyle($sheet, 7, '4A3400', 'FFD580', true,  $totalCols); // Answers (key)

// Student rows — alternating dark tones
for ($r = 8; $r <= $totalRows; $r++) {
    $bg = ($r % 2 === 0) ? '12121C' : '0E0E18';
    applyRowStyle($sheet, $r, $bg, 'D0CCC8', false, $totalCols);
}

// Bold labels Col A rows 1-7
$sheet->getStyle('A1:A7')->getFont()->setBold(true)->setSize(9);

// Freeze: col A + first 7 header rows
$sheet->freezePane('B8');

// Row heights
$sheet->getRowDimension(1)->setRowHeight(20);
for ($r = 2; $r <= 7; $r++) {
    $sheet->getRowDimension($r)->setRowHeight(18);
}

// ── Legend sheet ──────────────────────────────────────────────────────────
echo "Building legend sheet...\n";
$legend = $spreadsheet->createSheet();
$legend->setTitle('Legend & Instructions');

$legendRows = [
    ['ROW', 'LABEL', 'FILLED BY', 'DESCRIPTION'],
    ['1', 'RollNo / Q1..Q180', 'Auto (OMR software)', 'Column headers. Q1 was labelled "Answers" in old OMR format — system accepts both.'],
    ['2', 'SUBJECTS', 'Auto (OMR software)', 'Subject code per question: p=Physics  C=Chemistry  B=Biology  Z=Zoology'],
    ['3', 'TOPIC_CODES', '*** Sub-Admin fills this ***', 'Topic code per question from Curriculum Tree. Format: P-001, C-001, B-001, Z-001. Same code can repeat across columns if multiple Qs map to same topic.'],
    ['4', 'OC', 'Auto (OMR software)', 'Question type: C = Conventional MCQ. System reads but does not enforce.'],
    ['5', 'NEGATIVE', 'Auto (OMR software)', 'Negative marks per question (e.g. -1). Must be 0 or negative integer.'],
    ['6', 'MARKS', 'Auto (OMR software)', 'Positive marks for correct answer (e.g. 4). Must be positive integer.'],
    ['7', 'Answers', 'Auto (OMR software)', 'Answer key row. Values: 1, 2, 3, or 4. Use pipe separator for multiple correct: 1|2'],
    ['8+', 'Student roll numbers', 'Auto (OMR software)', 'One row per student. Values: 1/2/3/4 = option chosen   x = not attempted (0 marks)   * = invalid/smudge (-1 marks)'],
    ['', '', '', ''],
    ['MARKING RULES', '', '', ''],
    ['Correct answer', '+4', '', 'Student response matches key exactly (or matches any option in 1|2 format)'],
    ['Wrong answer', '-1', '', 'Student response does not match answer key'],
    ['Not attempted  x', '0', '', 'Student left question blank on OMR sheet'],
    ['Invalid  *', '-1', '', 'OMR detected double bubble or smudge — system treats same as wrong'],
    ['', '', '', ''],
    ['TOPIC CODE FORMAT', '', '', ''],
    ['Physics', 'P-001  to  P-045', '', 'Must match full_code stored in Vidya Curriculum Tree'],
    ['Chemistry', 'C-001  to  C-045', '', 'Must match full_code stored in Vidya Curriculum Tree'],
    ['Biology', 'B-001  to  B-045', '', 'Must match full_code stored in Vidya Curriculum Tree'],
    ['Zoology', 'Z-001  to  Z-045', '', 'Must match full_code stored in Vidya Curriculum Tree'],
    ['', '', '', ''],
    ['RULES', '', '', ''],
    ['1', 'TOPIC_CODES row (Row 3) must be filled before uploading — upload will fail validation if empty.', '', ''],
    ['2', 'Every topic code in Row 3 must exist in Vidya Curriculum Tree. Parser validates before any DB write.', '', ''],
    ['3', 'Same topic code CAN repeat across columns — multiple questions from same topic is valid.', '', ''],
    ['4', 'Roll numbers in Col A (Row 8+) must match roll numbers of students registered in Vidya.', '', ''],
    ['5', 'Do NOT delete, reorder, or rename Rows 1-7. Parser identifies rows by their Col A label text.', '', ''],
    ['6', 'Rows 1-7 in the Result Upload sheet are auto-generated by OMR software — do not edit.', '', ''],
    ['7', 'Only Row 3 (TOPIC_CODES) requires manual input by Sub-Admin before uploading.', '', ''],
];

foreach ($legendRows as $rIdx => $row) {
    foreach ($row as $cIdx => $val) {
        $legend->getCell(cellAddr($cIdx + 1, $rIdx + 1))->setValue($val);
    }
}

$legend->getColumnDimension('A')->setWidth(22);
$legend->getColumnDimension('B')->setWidth(22);
$legend->getColumnDimension('C')->setWidth(22);
$legend->getColumnDimension('D')->setWidth(85);

// Header row
$legend->getStyle('A1:D1')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A2E4A']],
]);

// TOPIC_CODES row (row 4)
$legend->getStyle('A4:D4')->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => '7EE8A2']],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D3B2E']],
]);

// Section header rows
foreach ([11, 17, 23] as $sRow) {
    $legend->getStyle("A{$sRow}:D{$sRow}")->applyFromArray([
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFD580']],
        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2A2000']],
    ]);
}

// Wrap text + row height on legend
$legend->getStyle('A1:D' . count($legendRows))->getAlignment()
    ->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
for ($r = 1; $r <= count($legendRows); $r++) {
    $legend->getRowDimension($r)->setRowHeight(30);
}
$legend->getRowDimension(4)->setRowHeight(45);

// ── Write Excel file ──────────────────────────────────────────────────────
$spreadsheet->setActiveSheetIndex(0);
echo "Writing file...\n";
$writer = new Xlsx($spreadsheet);
$writer->save($outputPath);

$students = $totalRows - 7; // rows minus 7 header rows
echo "\n✅  Done!\n";
echo "   Output : {$outputPath}\n";
echo "   Rows   : {$totalRows} (original " . ($totalRows - 1) . " + 1 TOPIC_CODES row inserted)\n";
echo "   Columns: {$totalCols}\n";
echo "   Students: {$students}\n";
echo "\n   Row layout:\n";
echo "   Row 1 → Header (RollNo + Q1..Q180)\n";
echo "   Row 2 → SUBJECTS\n";
echo "   Row 3 → TOPIC_CODES (P-001..P-045, C-001..C-045, B-001..B-045, Z-001..Z-045)\n";
echo "   Row 4 → OC\n";
echo "   Row 5 → NEGATIVE\n";
echo "   Row 6 → MARKS\n";
echo "   Row 7 → Answers (key)\n";
echo "   Row 8..{$totalRows} → Student responses\n";
