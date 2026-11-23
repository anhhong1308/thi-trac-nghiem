<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Hiển thị danh sách tài khoản
    public function index()
    {
        $users = User::latest()->get();

        return inertia('Admin/Users', [
            'users' => $users,
        ]);
    }

    // Thêm tài khoản mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in(['admin', 'teacher', 'student'])],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'account_status' => 'approved',
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Thêm tài khoản thành công!');
    }

    // Cập nhật tài khoản
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'teacher', 'student'])],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật tài khoản thành công!');
    }

    // Duyệt tài khoản giảng viên
    public function approveTeacher(User $user)
    {
        abort_unless(
            $user->role === 'teacher' && $user->account_status === 'pending',
            404
        );

        $user->update([
            'account_status' => 'approved',
        ]);

        return back()->with('success', 'Đã duyệt tài khoản giảng viên thành công!');
    }

    // Từ chối tài khoản giảng viên
    public function rejectTeacher(User $user)
    {
        abort_unless(
            $user->role === 'teacher' && $user->account_status === 'pending',
            404
        );

        $user->update([
            'account_status' => 'rejected',
        ]);

        return back()->with('success', 'Đã từ chối tài khoản giảng viên!');
    }

    // Xóa tài khoản
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể xóa tài khoản đang đăng nhập!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Xóa tài khoản thành công!');
    }
}
