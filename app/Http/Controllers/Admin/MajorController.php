<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MajorController extends Controller
{
    // Danh sách chuyên ngành
    public function index()
    {
        $majors = Major::with('subjects')
            ->withCount('subjects')
            ->latest()
            ->paginate(10);

        $subjects = Subject::orderBy('name')->get([
            'id',
            'name',
            'code',
            'is_active',
        ]);

        return Inertia::render('Admin/Majors/Index', [
            'majors' => $majors,
            'subjects' => $subjects,
        ]);
    }

    // Thêm chuyên ngành
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:majors,name'],
            'code' => ['required', 'string', 'max:255', 'unique:majors,code'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:subjects,id',
            ],
        ]);

        $major = Major::create([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        $major->subjects()->sync($validated['subject_ids'] ?? []);

        return redirect()
            ->route('admin.majors.index')
            ->with('success', 'Thêm chuyên ngành thành công.');
    }

    // Cập nhật chuyên ngành
    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('majors', 'name')->ignore($major->id),
            ],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('majors', 'code')->ignore($major->id),
            ],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
            'subject_ids' => ['nullable', 'array'],
            'subject_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:subjects,id',
            ],
        ]);

        $major->update([
            'name' => $validated['name'],
            'code' => $validated['code'],
            'description' => $validated['description'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        $major->subjects()->sync($validated['subject_ids'] ?? []);

        return redirect()
            ->route('admin.majors.index')
            ->with('success', 'Cập nhật chuyên ngành thành công.');
    }

    // Xóa chuyên ngành
    public function destroy(Major $major)
    {
        $major->delete();

        return redirect()
            ->route('admin.majors.index')
            ->with('success', 'Xóa chuyên ngành thành công.');
    }
}
