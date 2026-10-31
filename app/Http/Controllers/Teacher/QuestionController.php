<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\BloomLevel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    /**
     * Danh sách câu hỏi.
     */
    public function index()
    {
        $questions = Question::with([
            'subject',
            'chapter',
            'bloomLevel',
            'options',
        ])
            ->latest()
            ->paginate(10);

        return inertia('Teacher/Questions/Index', [
            'questions' => $questions,
            'flash' => [
                'success' => session('success'),
            ],
        ]);
    }

    /**
     * Form thêm câu hỏi.
     */
    public function create()
    {
        return inertia('Teacher/Questions/Create', [
            'subjects' => Subject::where('is_active', true)->get(),
            'chapters' => Chapter::where('is_active', true)->get(),
            'bloomLevels' => BloomLevel::orderBy('level')->get(),
        ]);
    }

    /**
     * Lưu câu hỏi mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'chapter_id' => [
                'required',
                'integer',
                'exists:chapters,id',
            ],
            'bloom_level_id' => [
                'required',
                'integer',
                'exists:bloom_levels,id',
            ],
            'content' => [
                'required',
                'string',
            ],
            'type' => [
                'required',
                Rule::in([
                    'single_choice',
                    'multiple_choice',
                ]),
            ],
            'explanation' => [
                'nullable',
                'string',
            ],
            'options' => [
                'required',
                'array',
                'min:2',
            ],
            'options.*.content' => [
                'required',
                'string',
            ],
            'options.*.is_correct' => [
                'required',
                'boolean',
            ],
        ], [
            'subject_id.required' => 'Vui lòng chọn môn học.',
            'chapter_id.required' => 'Vui lòng chọn chương.',
            'bloom_level_id.required' => 'Vui lòng chọn mức độ Bloom.',
            'content.required' => 'Vui lòng nhập nội dung câu hỏi.',
            'type.required' => 'Vui lòng chọn loại câu hỏi.',
            'options.required' => 'Vui lòng nhập các phương án.',
            'options.min' => 'Câu hỏi phải có ít nhất 2 phương án.',
            'options.*.content.required' => 'Nội dung phương án không được để trống.',
        ]);

        $this->validateQuestionData($validated);

        DB::transaction(function () use ($validated, $request) {
            $question = Question::create([
                'subject_id' => $validated['subject_id'],
                'chapter_id' => $validated['chapter_id'],
                'bloom_level_id' => $validated['bloom_level_id'],
                'created_by' => $request->user()->id,
                'content' => $validated['content'],
                'type' => $validated['type'],
                'explanation' => $validated['explanation'] ?? null,
                'status' => 'active',
            ]);

            $this->saveOptions($question, $validated['options']);
        });

        return redirect()
            ->route('teacher.questions.index')
            ->with('success', 'Thêm câu hỏi thành công.');
    }

    /**
     * Form sửa câu hỏi.
     */
    public function edit(Question $question)
    {
        $question->load('options');

        return inertia('Teacher/Questions/Edit', [
            'question' => $question,
            'subjects' => Subject::where('is_active', true)->get(),
            'chapters' => Chapter::where('is_active', true)->get(),
            'bloomLevels' => BloomLevel::orderBy('level')->get(),
        ]);
    }

    /**
     * Cập nhật câu hỏi.
     */
    public function update(Request $request, Question $question)
    {
        $validated = $request->validate([
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],
            'chapter_id' => [
                'required',
                'integer',
                'exists:chapters,id',
            ],
            'bloom_level_id' => [
                'required',
                'integer',
                'exists:bloom_levels,id',
            ],
            'content' => [
                'required',
                'string',
            ],
            'type' => [
                'required',
                Rule::in([
                    'single_choice',
                    'multiple_choice',
                ]),
            ],
            'explanation' => [
                'nullable',
                'string',
            ],
            'options' => [
                'required',
                'array',
                'min:2',
            ],
            'options.*.content' => [
                'required',
                'string',
            ],
            'options.*.is_correct' => [
                'required',
                'boolean',
            ],
        ], [
            'subject_id.required' => 'Vui lòng chọn môn học.',
            'chapter_id.required' => 'Vui lòng chọn chương.',
            'bloom_level_id.required' => 'Vui lòng chọn mức độ Bloom.',
            'content.required' => 'Vui lòng nhập nội dung câu hỏi.',
            'type.required' => 'Vui lòng chọn loại câu hỏi.',
            'options.required' => 'Vui lòng nhập các phương án.',
            'options.min' => 'Câu hỏi phải có ít nhất 2 phương án.',
            'options.*.content.required' => 'Nội dung phương án không được để trống.',
        ]);

        $this->validateQuestionData($validated);

        DB::transaction(function () use ($validated, $question) {
            $question->update([
                'subject_id' => $validated['subject_id'],
                'chapter_id' => $validated['chapter_id'],
                'bloom_level_id' => $validated['bloom_level_id'],
                'content' => $validated['content'],
                'type' => $validated['type'],
                'explanation' => $validated['explanation'] ?? null,
            ]);

            $question->options()->delete();

            $this->saveOptions($question, $validated['options']);
        });

        return redirect()
            ->route('teacher.questions.index')
            ->with('success', 'Cập nhật câu hỏi thành công.');
    }

    /**
     * Kiểm tra môn học, chương và đáp án đúng.
     */
    private function validateQuestionData(array $validated): void
    {
        $chapterExists = Chapter::where('id', $validated['chapter_id'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if (!$chapterExists) {
            throw ValidationException::withMessages([
                'chapter_id' => 'Chương không thuộc môn học đã chọn.',
            ]);
        }

        $correctCount = collect($validated['options'])
            ->filter(fn ($option) => filter_var(
                $option['is_correct'],
                FILTER_VALIDATE_BOOLEAN
            ))
            ->count();

        if ($validated['type'] === 'single_choice' && $correctCount !== 1) {
            throw ValidationException::withMessages([
                'options' => 'Câu hỏi một đáp án phải có chính xác 1 đáp án đúng.',
            ]);
        }

        if ($validated['type'] === 'multiple_choice' && $correctCount < 1) {
            throw ValidationException::withMessages([
                'options' => 'Câu hỏi nhiều đáp án phải có ít nhất 1 đáp án đúng.',
            ]);
        }
    }

    /**
     * Lưu danh sách phương án và thứ tự.
     */
    private function saveOptions(Question $question, array $options): void
    {
        foreach (array_values($options) as $index => $option) {
            $question->options()->create([
                'content' => $option['content'],
                'is_correct' => filter_var(
                    $option['is_correct'],
                    FILTER_VALIDATE_BOOLEAN
                ),
                'position' => $index + 1,
            ]);
        }
    }

    /**
     * Xóa câu hỏi.
     */
    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()
            ->route('teacher.questions.index')
            ->with('success', 'Xóa câu hỏi thành công.');
    }
}
