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
    Route::get('/invitations', [App\Http\Controllers\Yayasan\InvitationController::class, 'index'])->name('invitations');
    Route::post('/invitations/send', [App\Http\Controllers\Yayasan\InvitationController::class, 'send'])->name('invitations.send');
    Route::post('/invitations/send-bulk', [App\Http\Controllers\Yayasan\InvitationController::class, 'sendBulk'])->name('invitations.send_bulk');
    
    Route::get('/calendar/print', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'print'])->name('calendar.print');
    Route::get('/calendar', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'index'])->name('calendar.index');
    Route::post('/calendar', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'store'])->name('calendar.store');
    Route::put('/calendar/{calendar}', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'update'])->name('calendar.update');
    Route::delete('/calendar/{calendar}', [App\Http\Controllers\Yayasan\EducationalCalendarController::class, 'destroy'])->name('calendar.destroy');
});
