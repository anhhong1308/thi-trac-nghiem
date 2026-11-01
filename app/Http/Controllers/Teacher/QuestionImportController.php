<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\BloomLevel;
use App\Models\Chapter;
use App\Models\Question;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Throwable;

class QuestionImportController extends Controller
{
    /**
     * Trang import Excel.
     */
    public function index()
    {
        return Inertia::render('Teacher/Questions/Import', [
            'preview' => null,
        ]);
    }

    /**
     * Tải file Excel mẫu.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();

        // Sheet dữ liệu câu hỏi.
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('CauHoi');

        $headers = [
            'subject_code',
            'chapter_name',
            'bloom_code',
            'question_type',
            'question_content',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'correct_answers',
            'explanation',
        ];

        $sheet->fromArray($headers, null, 'A1');

        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getAlignment()
            ->setHorizontal('center');

        foreach (range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setWidth(25);
        }

        $sheet->getColumnDimension('E')->setWidth(50);
        $sheet->getColumnDimension('K')->setWidth(40);

        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:K1');

        // Sheet hướng dẫn.
        $guide = $spreadsheet->createSheet();
        $guide->setTitle('HuongDan');

        $instructions = [
            ['HƯỚNG DẪN NHẬP CÂU HỎI TỪ EXCEL'],
            ['Mỗi dòng trong sheet CauHoi tương ứng với một câu hỏi.'],
            ['subject_code: Mã môn học đã tồn tại trong hệ thống.'],
            ['chapter_name: Tên chương thuộc môn học đã chọn.'],
            ['bloom_code: REMEMBER, UNDERSTAND, APPLY, ANALYZE, EVALUATE hoặc CREATE.'],
            ['question_type: single_choice hoặc multiple_choice.'],
            ['question_content: Nội dung câu hỏi.'],
            ['option_a đến option_d: Nội dung các phương án. Cần ít nhất 2 phương án.'],
            ['correct_answers: Ghi chữ cái đáp án đúng, ví dụ A hoặc A,C hoặc B,D.'],
            ['explanation: Giải thích đáp án, có thể để trống.'],
            ['Câu hỏi single_choice chỉ được có đúng 1 đáp án đúng.'],
            ['Câu hỏi multiple_choice phải có ít nhất 1 đáp án đúng.'],
            ['Không thay đổi tên các cột trong sheet CauHoi.'],
            ['Xóa dòng hướng dẫn nếu bạn tự thêm dữ liệu vào sheet CauHoi.'],
        ];

        $guide->fromArray($instructions, null, 'A1');
        $guide->getStyle('A1')->getFont()->setBold(true);
        $guide->getColumnDimension('A')->setWidth(110);

        $spreadsheet->setActiveSheetIndex(0);

        return response()->streamDownload(
            function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            },
            'mau_import_cau_hoi.xlsx',
            [
                'Content-Type' =>
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]
        );
    }

    /**
     * Đọc file Excel và kiểm tra dữ liệu.
     */
    public function preview(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:5120',
            ],
        ], [
            'file.required' => 'Vui lòng chọn file Excel.',
            'file.mimes' => 'Chỉ hỗ trợ file Excel .xlsx hoặc .xls.',
            'file.max' => 'Dung lượng file không được vượt quá 5MB.',
        ]);

        try {
            $spreadsheet = IOFactory::load(
                $request->file('file')->getRealPath()
            );

            $sheet = $spreadsheet->getSheetByName('CauHoi');

            if (!$sheet) {
                return back()->withErrors([
                    'file' => 'Không tìm thấy sheet có tên CauHoi trong file Excel.',
                ]);
            }

            $excelRows = $sheet->toArray(null, true, false, false);
        } catch (Throwable $e) {
            return back()->withErrors([
                'file' => 'Không thể đọc file Excel. Vui lòng kiểm tra lại file.',
            ]);
        }

        $headers = [
            'subject_code',
            'chapter_name',
            'bloom_code',
            'question_type',
            'question_content',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'correct_answers',
            'explanation',
        ];

        $actualHeaders = array_map(
            fn ($value) => strtolower(trim((string) $value)),
            array_slice($excelRows[0] ?? [], 0, count($headers))
        );

        if ($actualHeaders !== $headers) {
            return back()->withErrors([
                'file' => 'Tên hoặc thứ tự các cột trong file Excel không đúng mẫu. Vui lòng tải lại file mẫu.',
            ]);
        }

        $previewRows = [];
        $validRows = [];
        $errorCount = 0;

        foreach (array_slice($excelRows, 1) as $index => $row) {
            $row = array_pad($row, count($headers), null);

            $data = [];

            foreach ($headers as $columnIndex => $header) {
                $data[$header] = trim((string) ($row[$columnIndex] ?? ''));
            }

            // Bỏ qua dòng hoàn toàn trống.
            if (count(array_filter($data, fn ($value) => $value !== '')) === 0) {
                continue;
            }

            // Số dòng thực tế trong Excel.
            $rowNumber = $index + 2;

            $errors = [];

            // Tìm môn học.
            $subject = Subject::where(
                'code',
                $data['subject_code']
            )->first();

            if (!$subject) {
                $errors[] = "Mã môn học '{$data['subject_code']}' không tồn tại.";
            }

            // Tìm chương thuộc môn học.
            $chapter = null;

            if ($subject) {
                $chapter = Chapter::where('subject_id', $subject->id)
                    ->where('name', $data['chapter_name'])
                    ->first();
            }

            if (!$chapter) {
                $errors[] = 'Tên chương không tồn tại hoặc không thuộc môn học đã chọn.';
            }

            // Tìm mức độ Bloom.
            $bloomLevel = BloomLevel::where(
                'code',
                strtoupper($data['bloom_code'])
            )->first();

            if (!$bloomLevel) {
                $errors[] = "Mã Bloom '{$data['bloom_code']}' không hợp lệ.";
            }

            // Kiểm tra loại câu hỏi.
            $type = strtolower($data['question_type']);

            if (!in_array($type, ['single_choice', 'multiple_choice'], true)) {
                $errors[] = 'Loại câu hỏi phải là single_choice hoặc multiple_choice.';
            }

            // Kiểm tra nội dung câu hỏi.
            if ($data['question_content'] === '') {
                $errors[] = 'Nội dung câu hỏi không được để trống.';
            }

            // Lấy các phương án có nội dung.
            $options = [];

            foreach (['A', 'B', 'C', 'D'] as $letter) {
                $content = $data['option_' . strtolower($letter)];

                if ($content !== '') {
                    $options[$letter] = $content;
                }
            }

            if (count($options) < 2) {
                $errors[] = 'Câu hỏi phải có ít nhất 2 phương án.';
            }

            // Phân tích đáp án đúng.
            $correctAnswers = array_filter(
                preg_split(
                    '/[\s,;]+/',
                    strtoupper($data['correct_answers'])
                )
            );

            $correctAnswers = array_values(array_unique($correctAnswers));

            if (count($correctAnswers) === 0) {
                $errors[] = 'Vui lòng nhập đáp án đúng.';
            }

            foreach ($correctAnswers as $answer) {
                if (!in_array($answer, ['A', 'B', 'C', 'D'], true)) {
                    $errors[] = "Ký hiệu đáp án '{$answer}' không hợp lệ.";
                    continue;
                }

                if (!isset($options[$answer])) {
                    $errors[] = "Đáp án {$answer} không có nội dung.";
                }
            }

            if (
                $type === 'single_choice' &&
                count($correctAnswers) !== 1
            ) {
                $errors[] = 'Câu hỏi một đáp án phải có chính xác 1 đáp án đúng.';
            }

            if (
                $type === 'multiple_choice' &&
                count($correctAnswers) < 1
            ) {
                $errors[] = 'Câu hỏi nhiều đáp án phải có ít nhất 1 đáp án đúng.';
            }

            $preparedOptions = [];

            foreach ($options as $letter => $content) {
                $preparedOptions[] = [
                    'content' => $content,
                    'is_correct' => in_array(
                        $letter,
                        $correctAnswers,
                        true
                    ),
                ];
            }

            $valid = count($errors) === 0;

            if (!$valid) {
                $errorCount++;
            } else {
                $validRows[] = [
                    'subject_id' => $subject->id,
                    'chapter_id' => $chapter->id,
                    'bloom_level_id' => $bloomLevel->id,
                    'content' => $data['question_content'],
                    'type' => $type,
                    'explanation' => $data['explanation'] ?: null,
                    'options' => $preparedOptions,
                ];
            }

            $previewRows[] = [
                'row_number' => $rowNumber,
                'subject_code' => $data['subject_code'],
                'chapter_name' => $data['chapter_name'],
                'bloom_code' => strtoupper($data['bloom_code']),
                'content' => $data['question_content'],
                'type' => $type,
                'options' => $preparedOptions,
                'correct_answers' => implode(', ', $correctAnswers),
                'valid' => $valid,
                'errors' => $errors,
            ];
        }

        if (count($previewRows) === 0) {
            return back()->withErrors([
                'file' => 'File Excel chưa có dữ liệu câu hỏi.',
            ]);
        }

        // Lưu dữ liệu xem trước trong session.
        session()->put('question_import_preview', [
            'rows' => $validRows,
            'has_errors' => $errorCount > 0,
        ]);

        return Inertia::render('Teacher/Questions/Import', [
            'preview' => [
                'rows' => $previewRows,
                'total' => count($previewRows),
                'valid' => count($previewRows) - $errorCount,
                'invalid' => $errorCount,
            ],
        ]);
    }

    /**
     * Xác nhận và lưu câu hỏi vào database.
     */
    public function confirm(Request $request)
    {
        $preview = session()->get('question_import_preview');

        if (!$preview) {
            return redirect()
                ->route('teacher.questions.import')
                ->withErrors([
                    'file' => 'Không tìm thấy dữ liệu xem trước. Vui lòng tải file Excel lại.',
                ]);
        }

        if ($preview['has_errors']) {
            return redirect()
                ->route('teacher.questions.import')
                ->withErrors([
                    'file' => 'File còn dữ liệu không hợp lệ. Vui lòng sửa lỗi trước khi nhập.',
                ]);
        }

        $rows = $preview['rows'];

        DB::transaction(function () use ($rows, $request) {
            foreach ($rows as $row) {
                $question = Question::create([
                    'subject_id' => $row['subject_id'],
                    'chapter_id' => $row['chapter_id'],
                    'bloom_level_id' => $row['bloom_level_id'],
                    'created_by' => $request->user()->id,
                    'content' => $row['content'],
                    'type' => $row['type'],
                    'explanation' => $row['explanation'],
                    'status' => 'active',
                ]);

                foreach ($row['options'] as $index => $option) {
                    $question->options()->create([
                        'content' => $option['content'],
                        'is_correct' => $option['is_correct'],
                        'position' => $index + 1,
                    ]);
                }
            }
        });

        $count = count($rows);

        session()->forget('question_import_preview');

        return redirect()
            ->route('teacher.questions.index')
            ->with('success', "Import thành công {$count} câu hỏi.");
    }
}