<?php

use App\Http\Controllers\BmiRecordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MyBmiController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountApprovalController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\SelfAssessmentController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authenticated routes
Route::middleware(['auth'])->group(function () {

    // Dashboard (all users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.update-photo');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.remove-photo');

    // Reports (all users)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.export-pdf');
    Route::get('/reports/export-excel', [ReportController::class, 'exportExcel'])->name('reports.export-excel');

    // Officer - My BMI History
    Route::get('/my-bmi', [MyBmiController::class, 'index'])->name('my-bmi.index');

    // Officer - Self Assessment
    Route::middleware(['role:officer'])->group(function () {
        Route::get('/self-assessment', [SelfAssessmentController::class, 'create'])->name('self-assessment.create');
        Route::post('/self-assessment', [SelfAssessmentController::class, 'store'])->name('self-assessment.store');
        Route::get('/self-assessment/edit', [SelfAssessmentController::class, 'edit'])->name('self-assessment.edit');
        Route::put('/self-assessment', [SelfAssessmentController::class, 'update'])->name('self-assessment.update');
    });

    // Admin-only routes
    Route::middleware(['role:admin'])->group(function () {
        Route::get('account-approval', [AccountApprovalController::class, 'index'])->name('account-approval.index');
        Route::patch('account-approval/{user}/approve', [AccountApprovalController::class, 'approve'])->name('account-approval.approve');
        Route::delete('account-approval/{user}/reject', [AccountApprovalController::class, 'reject'])->name('account-approval.reject');
        Route::get('personnel/archived', [PersonnelController::class, 'archived'])->name('personnel.archived');
        Route::patch('personnel/{id}/restore', [PersonnelController::class, 'restore'])->name('personnel.restore');
        Route::delete('personnel/{id}/force-delete', [PersonnelController::class, 'forceDelete'])->name('personnel.force-delete');
        Route::resource('personnel', PersonnelController::class)->except(['create', 'store']);
        Route::get('bmi-records/archived', [BmiRecordController::class, 'archived'])->name('bmi-records.archived');
        Route::patch('bmi-records/{id}/restore', [BmiRecordController::class, 'restore'])->name('bmi-records.restore');
        Route::resource('bmi-records', BmiRecordController::class);
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

});

require __DIR__.'/auth.php';
