<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SubjectController extends Controller
{
    // Danh sách môn học
    public function index()
    {
        $subjects = Subject::withCount('chapters')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Subjects/Index', [
            'subjects' => $subjects,
        ]);
    }

    // Thêm môn học
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:subjects,code'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ], [
            'name.required' => 'Vui lòng nhập tên môn học.',
            'code.required' => 'Vui lòng nhập mã môn học.',
            'code.unique' => 'Mã môn học đã tồn tại.',
        ]);

        Subject::create($validated);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Thêm môn học thành công.');
    }

    // Cập nhật môn học
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects', 'code')->ignore($subject->id),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
        ], [
            'name.required' => 'Vui lòng nhập tên môn học.',
            'code.required' => 'Vui lòng nhập mã môn học.',
            'code.unique' => 'Mã môn học đã tồn tại.',
        ]);

        $subject->update($validated);

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Cập nhật môn học thành công.');
    }

    // Xóa môn học
    public function destroy(Subject $subject)
    {
        if ($subject->chapters()->exists()) {
            return back()->with(
                'error',
                'Không thể xóa môn học đã có chương. Vui lòng xử lý các chương trước.'
            );
        }

        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Xóa môn học thành công.');
    }
}
