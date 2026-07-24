<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Yayasan (Ketua Yayasan) Routes
|--------------------------------------------------------------------------
| Routes for the foundation chairman (Ketua Yayasan).
| Prefix: /yayasan   |  Name: yayasan.*
| Middleware: auth, yayasan
*/

Route::prefix('yayasan')->name('yayasan.')->middleware('auth', 'yayasan')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Yayasan\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/progress-input', [App\Http\Controllers\Yayasan\ProgressInputController::class, 'index'])->name('progress-input');
    Route::get('/progress-input/export-pdf', [App\Http\Controllers\Yayasan\ProgressInputController::class, 'exportPdf'])->name('progress-input.export-pdf');
    Route::get('/invitations', [App\Http\Controllers\Yayasan\InvitationController::class, 'index'])->name('invitations');
    Route::post('/invitations/send', [App\Http\Controllers\Yayasan\InvitationController::class, 'send'])->name('invitations.send');
    Route::post('/invitations/send-bulk', [App\Http\Controllers\Yayasan\InvitationController::class, 'sendBulk'])->name('invitations.send_bulk');
    
    Route::get('/calendar/print', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'print'])->name('calendar.print');
    Route::get('/calendar/monday-inspiration/print', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'printMondayInspiration'])->name('calendar.monday_inspiration.print');
    Route::get('/calendar', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'store'])->name('calendar.store');
    Route::put('/calendar/{calendar}', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'update'])->name('calendar.update');
    Route::delete('/calendar/{calendar}', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'destroy'])->name('calendar.destroy');

    // Finalisasi Perjanjian Kinerja (Satu Controller dengan Admin)
    Route::prefix('performance-contracts')->name('performance_contracts.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PerformanceContractController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\PerformanceContractController::class, 'show'])->name('show');
        Route::post('/{id}/process', [App\Http\Controllers\Admin\PerformanceContractController::class, 'process'])->name('process');
        Route::delete('/{id}', [App\Http\Controllers\Admin\PerformanceContractController::class, 'destroy'])->name('destroy');
    });

    // Saldo Kontribusi Akhir Unit Sekolah (Yayasan)
    Route::prefix('saldo-kontribusi')->name('contribution_balance.')->group(function () {
        Route::get('/', [App\Http\Controllers\Yayasan\ContributionBalanceController::class, 'index'])->name('index');
        Route::post('/save', [App\Http\Controllers\Yayasan\ContributionBalanceController::class, 'store'])->name('store');
        Route::get('/export-pdf', [App\Http\Controllers\Yayasan\ContributionBalanceController::class, 'exportPdf'])->name('export_pdf');
    });

    // Evaluasi Perjanjian Kinerja Akhir Semester (Satu Controller dengan Admin)
    Route::get('/performance-evaluations', [App\Http\Controllers\Admin\PerformanceEvaluationController::class, 'index'])->name('performance_evaluations.index');
    Route::get('/performance-evaluations/{contractId}/{semesterId}/evaluate', [App\Http\Controllers\Admin\PerformanceEvaluationController::class, 'evaluate'])->name('performance_evaluations.evaluate');
    Route::post('/performance-evaluations/{contractId}/{semesterId}', [App\Http\Controllers\Admin\PerformanceEvaluationController::class, 'store'])->name('performance_evaluations.store');

    // PKL Monitoring Reports (Yayasan uses Admin controller)
    Route::prefix('pkl-monitorings')->name('pkl_monitorings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\PklMonitoringReportController::class, 'index'])->name('index');
        Route::get('/{teacher}', [App\Http\Controllers\Admin\PklMonitoringReportController::class, 'show'])->name('show');
    });
});
