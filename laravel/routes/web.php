<?php

use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\CurriculumController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\SubAdmin\DashboardController as SubAdminDashboardController;
use App\Http\Controllers\SubAdmin\StudentController as SubAdminStudentController;
use App\Http\Controllers\SubAdmin\TestController as SubAdminTestController;
use App\Http\Controllers\SubAdmin\UploadController as SubAdminUploadController;
use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// --- Public Routes ---
Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
Route::post('/forgot-password', function () {
    return back()->with('status', 'If that email exists, a reset link has been sent.');
})->name('password.email');

// --- Protected Routes ---
Route::middleware(['auth', 'scope.institute'])->group(function () {

    // Admin
    Route::prefix('admin')->middleware('role:admin,owner')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        // Courses
        Route::get('/courses',                 [CourseController::class, 'index'])->name('admin.courses');
        Route::post('/courses',                [CourseController::class, 'store'])->name('admin.courses.store');
        Route::put('/courses/{id}',            [CourseController::class, 'update'])->name('admin.courses.update');
        Route::patch('/courses/{id}/toggle',   [CourseController::class, 'toggle'])->name('admin.courses.toggle');
        Route::delete('/courses/{id}',         [CourseController::class, 'destroy'])->name('admin.courses.destroy');

        // Subjects
        Route::get('/subjects',                [SubjectController::class, 'index'])->name('admin.subjects');
        Route::post('/subjects',               [SubjectController::class, 'store'])->name('admin.subjects.store');
        Route::put('/subjects/{id}',           [SubjectController::class, 'update'])->name('admin.subjects.update');
        Route::patch('/subjects/{id}/toggle',  [SubjectController::class, 'toggle'])->name('admin.subjects.toggle');
        Route::delete('/subjects/{id}',        [SubjectController::class, 'destroy'])->name('admin.subjects.destroy');

        // Batches
        Route::get('/batches',                 [BatchController::class, 'index'])->name('admin.batches');
        Route::post('/batches',                [BatchController::class, 'store'])->name('admin.batches.store');
        Route::put('/batches/{id}',            [BatchController::class, 'update'])->name('admin.batches.update');
        Route::patch('/batches/{id}/toggle',   [BatchController::class, 'toggle'])->name('admin.batches.toggle');
        Route::delete('/batches/{id}',         [BatchController::class, 'destroy'])->name('admin.batches.destroy');

        // Students
        Route::get('/students',                [StudentController::class, 'index'])->name('admin.students');
        Route::post('/students',               [StudentController::class, 'store'])->name('admin.students.store');
        Route::put('/students/{id}',           [StudentController::class, 'update'])->name('admin.students.update');
        Route::patch('/students/{id}/toggle',  [StudentController::class, 'toggle'])->name('admin.students.toggle');
        Route::delete('/students/{id}',        [StudentController::class, 'destroy'])->name('admin.students.destroy');

        // Assignments
        Route::get('/assignments',         [AssignmentController::class, 'index'])->name('admin.assignments');
        Route::post('/assignments/toggle', [AssignmentController::class, 'toggle'])->name('admin.assignments.toggle');
        Route::post('/assignments/bulk',   [AssignmentController::class, 'bulk'])->name('admin.assignments.bulk');

        // Curriculum
        Route::get('/curriculum',          [CurriculumController::class, 'index'])->name('admin.curriculum');
        Route::post('/curriculum',         [CurriculumController::class, 'store'])->name('admin.curriculum.store');
        Route::put('/curriculum/{id}',     [CurriculumController::class, 'update'])->name('admin.curriculum.update');
        Route::delete('/curriculum/{id}',  [CurriculumController::class, 'destroy'])->name('admin.curriculum.destroy');

        // Staff
        Route::get('/staff',                   [StaffController::class, 'index'])->name('admin.staff');
        Route::post('/staff',                  [StaffController::class, 'store'])->name('admin.staff.store');
        Route::put('/staff/{id}',              [StaffController::class, 'update'])->name('admin.staff.update');
        Route::patch('/staff/{id}/toggle',     [StaffController::class, 'toggle'])->name('admin.staff.toggle');
        Route::delete('/staff/{id}',           [StaffController::class, 'destroy'])->name('admin.staff.destroy');

        // Settings
        Route::get('/settings',                [SettingsController::class, 'index'])->name('admin.settings');
        Route::post('/settings/profile',       [SettingsController::class, 'updateProfile'])->name('admin.settings.profile');
        Route::post('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('admin.settings.notifications');
        Route::post('/settings/ai',            [SettingsController::class, 'updateAI'])->name('admin.settings.ai');

        // Audit Log
        Route::get('/audit-log',               [AuditLogController::class, 'index'])->name('admin.audit-log');

        // Notifications
        Route::get('/notifications',                      [NotificationController::class, 'index'])->name('admin.notifications');
        Route::patch('/notifications/{id}/read',          [NotificationController::class, 'markRead'])->name('admin.notifications.read');
        Route::post('/notifications/read-all',            [NotificationController::class, 'markAllRead'])->name('admin.notifications.read-all');
    });

    // Academic Head
    Route::prefix('academic-head')->middleware('role:academic_head,owner')->group(function () {
        Route::get('/dashboard', fn() => 'Academic Head Dashboard — coming soon')->name('academic-head.dashboard');
    });

    // Owner
    Route::prefix('owner')->middleware('role:owner')->group(function () {
        Route::get('/dashboard', fn() => 'Owner Dashboard — coming soon')->name('owner.dashboard');
    });

    // Teacher
    Route::prefix('teacher')->middleware('role:teacher')->group(function () {
        Route::get('/dashboard', fn() => 'Teacher Dashboard — coming soon')->name('teacher.dashboard');
    });

    // Typist
    Route::prefix('typist')->middleware('role:typist')->group(function () {
        Route::get('/dashboard', fn() => 'Typist Dashboard — coming soon')->name('typist.dashboard');
    });

    // Sub-Admin
    Route::prefix('sub-admin')->middleware('role:sub_admin,admin,owner')->group(function () {
        Route::get('/dashboard', [SubAdminDashboardController::class, 'index'])->name('sub-admin.dashboard');

        // Tests
        Route::get('/tests/create',  [SubAdminTestController::class, 'create'])->name('sub-admin.tests.create');
        Route::post('/tests',        [SubAdminTestController::class, 'store'])->name('sub-admin.tests.store');
        Route::get('/tests',         [SubAdminTestController::class, 'index'])->name('sub-admin.tests.index');

        // Results — Upload wizard
        Route::get('/results/upload',          [SubAdminUploadController::class, 'selectTest'])->name('sub-admin.results.upload');
        Route::post('/results/select',         [SubAdminUploadController::class, 'goToUpload'])->name('sub-admin.results.select');
        Route::get('/results/upload/{test}/file',    [SubAdminUploadController::class, 'showUploadForm'])->name('sub-admin.results.upload.file');
        Route::post('/results/upload/{test}/file',   [SubAdminUploadController::class, 'processUpload'])->name('sub-admin.results.upload.process');
        Route::get('/results/upload/{test}/map',     [SubAdminUploadController::class, 'showMapping'])->name('sub-admin.results.upload.map');
        Route::post('/results/upload/{test}/map',    [SubAdminUploadController::class, 'saveMapping'])->name('sub-admin.results.upload.save-map');
        Route::get('/results/upload/{test}/analyze', [SubAdminUploadController::class, 'showAnalyze'])->name('sub-admin.results.upload.analyze');
        Route::post('/results/upload/{test}/analyze',[SubAdminUploadController::class, 'runAnalysis'])->name('sub-admin.results.upload.run');
        Route::get('/results/history',               fn() => 'Upload History — coming soon')->name('sub-admin.results.history');

        // Students (read-only)
        Route::get('/students', [SubAdminStudentController::class, 'index'])->name('sub-admin.students');

        // Help (stub)
        Route::get('/help', fn() => 'Help — coming soon')->name('sub-admin.help');
    });
});
