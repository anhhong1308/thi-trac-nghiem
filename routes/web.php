
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Teacher\QuestionController;
use App\Http\Controllers\Teacher\QuestionImportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// ==========================================
// TRANG CHỦ
// ==========================================
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// ==========================================
// ĐIỀU HƯỚNG NGƯỜI DÙNG THEO VAI TRÒ
// ==========================================
Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    return match ($role) {
        'admin' => redirect()->route('admin.dashboard'),
        'teacher' => redirect()->route('teacher.dashboard'),
        default => redirect()->route('student.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// ==========================================
// KHU VỰC ADMIN
// ==========================================
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard Admin
        Route::get('/', function () {
            return Inertia::render('Admin/Dashboard');
        })->name('dashboard');

        // Quản lý tài khoản
        Route::resource('users', UserController::class)
            ->except(['create', 'edit', 'show']);

        // Quản lý môn học
        Route::resource('subjects', SubjectController::class)
            ->except(['create', 'edit', 'show']);
    });

// ==========================================
// KHU VỰC GIẢNG VIÊN
// ==========================================
Route::middleware(['auth', 'verified', 'role:teacher'])
    ->prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // Dashboard Giảng viên
        Route::get('/', function () {
            return Inertia::render('Teacher/Dashboard');
        })->name('dashboard');

        // Import câu hỏi từ Excel
        Route::get(
            '/questions/import',
            [QuestionImportController::class, 'index']
        )->name('questions.import');

        // Tải file Excel mẫu
        Route::get(
            '/questions/import/template',
            [QuestionImportController::class, 'downloadTemplate']
        )->name('questions.import.template');

        // Tải file Excel lên và xem trước
        Route::post(
            '/questions/import/preview',
            [QuestionImportController::class, 'preview']
        )->name('questions.import.preview');

        // Xác nhận nhập câu hỏi
        Route::post(
            '/questions/import/confirm',
            [QuestionImportController::class, 'confirm']
        )->name('questions.import.confirm');

        // Ngân hàng câu hỏi: thêm, sửa, xóa, danh sách
        Route::resource('questions', QuestionController::class)
            ->except(['show']);
    });

// ==========================================
// KHU VỰC SINH VIÊN
// ==========================================
Route::middleware(['auth', 'verified', 'role:student'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {

        // Dashboard Sinh viên
        Route::get('/', function () {
            return Inertia::render('Student/Dashboard');
        })->name('dashboard');
    });

// ==========================================
// QUẢN LÝ HỒ SƠ CÁ NHÂN
// ==========================================
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

// ==========================================
// AUTHENTICATION
// ==========================================
require __DIR__.'/auth.php';
