<?php

use App\Http\Controllers\Student\ProgressReportController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\StudentPrintController;

Route::get('/students/{record}/print', [StudentPrintController::class, 'print'])->name('students.print');


Route::middleware(['auth'])->prefix('student')->group(function () {
    // Route::get('/progress', [ProgressReportController::class, 'index'])
    //     ->name('student.progress');

    // Route::get('/progress/{report}/pdf', [ProgressReportController::class, 'pdf'])
    //     ->name('student.progress.pdf');
});

Route::middleware(['auth'])->group(function () {
    // Quick Sidebar Logout
    Route::get('/system/logout', function() {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/admin/login');
    })->name('system.logout');

    // System Backup & Export (Downloads SQLite Database)
    Route::get('/system/backup', function() {
        if (!auth()->user()?->hasRole('admin')) { abort(403); }
        return response()->download(database_path('database.sqlite'), 'moyosafi_full_backup_' . date('Y_m_d_H_i') . '.sqlite');
    })->name('system.backup');
});
