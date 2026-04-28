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
use App\Http\Controllers\SubAdmin\ResultController as SubAdminResultController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\AcademicHead\DashboardController as AHDashboardController;
use App\Http\Controllers\AcademicHead\AcademicHeadController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\Reception\ReceptionController;
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
        Route::get('/students',                     [StudentController::class, 'index'])->name('admin.students');
        Route::post('/students',                    [StudentController::class, 'store'])->name('admin.students.store');
        Route::put('/students/{id}',                [StudentController::class, 'update'])->name('admin.students.update');
        Route::patch('/students/{id}/toggle',       [StudentController::class, 'toggle'])->name('admin.students.toggle');
        Route::delete('/students/{id}',             [StudentController::class, 'destroy'])->name('admin.students.destroy');
        Route::post('/students/import',             [StudentController::class, 'importExcel'])->name('admin.students.import');
        Route::get('/students/import/template',     [StudentController::class, 'downloadTemplate'])->name('admin.students.import.template');

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
        Route::get('/dashboard',              [AHDashboardController::class,    'index'])->name('academic-head.dashboard');
        Route::get('/curriculum-coverage',    [AcademicHeadController::class,  'curriculumCoverage'])->name('academic-head.curriculum-coverage');
        Route::get('/test-quality',           [AcademicHeadController::class,  'testQuality'])->name('academic-head.test-quality');
        Route::get('/subject-performance',                        [AcademicHeadController::class, 'subjectPerformance'])->name('academic-head.subject-performance');
        Route::get('/subject-performance/{subject_id}/comparison', [AcademicHeadController::class, 'subjectComparison'])->name('academic-head.subject-comparison');
        Route::get('/teacher-effectiveness',              [AcademicHeadController::class, 'teacherEffectiveness'])->name('academic-head.teacher-effectiveness');
        Route::get('/teacher-effectiveness/{teacher_id}', [AcademicHeadController::class, 'teacherDeepDive'])->name('academic-head.teacher-deep-dive');
        Route::get('/teacher-assignments',    [AcademicHeadController::class,  'teacherAssignments'])->name('academic-head.teacher-assignments');
        Route::get('/at-risk-students',       [AcademicHeadController::class,  'atRiskStudents'])->name('academic-head.at-risk-students');
        Route::get('/notifications',          [AcademicHeadController::class,  'notifications'])->name('academic-head.notifications');
        Route::get('/help',                   [AcademicHeadController::class,  'help'])->name('academic-head.help');
    });

    // Owner
    Route::prefix('owner')->middleware('role:owner,admin')->group(function () {
        Route::get('/dashboard',          [OwnerDashboardController::class, 'index'])->name('owner.dashboard');
        Route::get('/course-performance', [OwnerController::class, 'coursePerformance'])->name('owner.course-performance');
        Route::get('/subject-roi',        [OwnerController::class, 'subjectRoi'])->name('owner.subject-roi');
        Route::get('/subject-roi/{subject_id}', [OwnerController::class, 'subjectRoiDetail'])->name('owner.subject-roi.detail');
        Route::get('/financial',          [OwnerController::class, 'financial'])->name('owner.financial');
        Route::get('/teachers',           [OwnerController::class, 'teachers'])->name('owner.teachers');
        Route::get('/teachers/{teacher_id}', [OwnerController::class, 'teacherDeepDive'])->name('owner.teacher-deep-dive');
        Route::get('/staff-decisions',    [OwnerController::class, 'staffDecisions'])->name('owner.staff-decisions');
        Route::get('/strategic-alerts',   [OwnerController::class, 'strategicAlerts'])->name('owner.strategic-alerts');
        Route::get('/at-risk-students',   [OwnerController::class, 'atRiskStudents'])->name('owner.at-risk-students');
        Route::get('/notifications',      [OwnerController::class, 'notifications'])->name('owner.notifications');
        Route::get('/help',               [OwnerController::class, 'help'])->name('owner.help');
    });

    // Teacher
    Route::prefix('teacher')->middleware('role:teacher')->group(function () {
        Route::get('/dashboard',              [TeacherDashboardController::class, 'index'])->name('teacher.dashboard');
        Route::get('/students',               [TeacherController::class, 'students'])->name('teacher.students');
        Route::get('/students/{student}',     [TeacherController::class, 'studentDetail'])->name('teacher.students.detail');
        Route::get('/heatmap',                [TeacherController::class, 'heatmap'])->name('teacher.heatmap');
        Route::get('/insights',               [TeacherController::class, 'insights'])->name('teacher.insights');
        Route::get('/weak-topics',            [TeacherController::class, 'weakTopics'])->name('teacher.weak-topics');
        Route::get('/topics/{topic_code}',    [TeacherController::class, 'topicDetail'])->name('teacher.topics.detail');
        Route::get('/tests',                  [TeacherController::class, 'tests'])->name('teacher.tests');
        Route::get('/notifications',          [TeacherController::class, 'notifications'])->name('teacher.notifications');
        Route::get('/help',                   [TeacherController::class, 'help'])->name('teacher.help');
    });

    // Reception
    Route::prefix('reception')->middleware('role:reception,admin,owner')->group(function () {
        Route::get('/dashboard',                            [ReceptionController::class, 'dashboard'])->name('reception.dashboard');
        Route::get('/students',                             [ReceptionController::class, 'students'])->name('reception.students');
        Route::get('/tests',                                [ReceptionController::class, 'tests'])->name('reception.tests');
        Route::get('/tests/{test_id}',                      [ReceptionController::class, 'testResults'])->name('reception.test-results');
        Route::get('/students/{student_id}/tests/{test_id}',[ReceptionController::class, 'studentResult'])->name('reception.student-result');
        Route::get('/walk-ins',                             [ReceptionController::class, 'walkIns'])->name('reception.walk-ins');
        Route::get('/help',                                 [ReceptionController::class, 'help'])->name('reception.help');
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
        Route::get('/results/history',               [SubAdminResultController::class, 'history'])->name('sub-admin.results.history');
        Route::get('/tests/{test}/results',          [SubAdminResultController::class, 'testResults'])->name('sub-admin.tests.results');
        Route::get('/tests/{test}/results/{student}',[SubAdminResultController::class, 'studentResult'])->name('sub-admin.tests.student-result');

        // Students (read-only)
        Route::get('/students', [SubAdminStudentController::class, 'index'])->name('sub-admin.students');

        // Help (stub)
        Route::get('/help', fn() => 'Help — coming soon')->name('sub-admin.help');
    });
});
