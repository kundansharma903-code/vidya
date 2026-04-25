<?php

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
        Route::get('/dashboard', fn() => 'Admin Dashboard — coming soon')->name('admin.dashboard');
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
        Route::get('/dashboard', fn() => 'Sub-Admin Dashboard — coming soon')->name('sub-admin.dashboard');
    });
});
