<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChapterController extends Controller
{
    /**
     * Danh sách chương.
     */
    public function index()
    {
        $chapters = Chapter::with('subject')
            ->orderBy('subject_id')
            ->orderBy('order')
            ->paginate(10);

        $subjects = Subject::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return Inertia::render('Teacher/Chapters/Index', [
            'chapters' => $chapters,
            'subjects' => $subjects,
        ]);
    }

    /**
     * Thêm chương.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ]);

        Chapter::create($validated);

        return redirect()
            ->route('teacher.chapters.index')
            ->with('success', 'Thêm chương thành công!');
    }

    /**
     * Cập nhật chương.
     */
    public function update(Request $request, Chapter $chapter)
    {
        $validated = $request->validate([
            'subject_id' => ['required', 'exists:subjects,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'order' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'boolean'],
        ]);

        $chapter->update($validated);

        return redirect()
            ->route('teacher.chapters.index')
            ->with('success', 'Cập nhật chương thành công!');
    }

    /**
     * Xóa chương.
     */
    public function destroy(Chapter $chapter)
    {
        if ($chapter->questions()->exists()) {
            return redirect()
                ->route('teacher.chapters.index')
                ->with('error', 'Không thể xóa chương đang có câu hỏi!');
        }

        $chapter->delete();

        return redirect()
            ->route('teacher.chapters.index')
            ->with('success', 'Xóa chương thành công!');
    }
}