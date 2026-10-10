<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StaffController;
use App\Http\Middleware\EnsureSchoolAccess;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthController::class, 'create'])->name('login');
    Route::get('/login', fn () => redirect()->route('login'));
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});
Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/admin/dashboard', [SchoolController::class, 'index'])->name('dashboard');
    Route::get('/admin/staff', [SchoolController::class, 'legacyStaff']);
    Route::prefix('schools/{school}')->middleware(EnsureSchoolAccess::class)->group(function (): void {
        Route::post('/select', [SchoolController::class, 'select'])->name('schools.select');
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->whereNumber('staff')->name('staff.edit');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->whereNumber('staff')->name('staff.update');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->whereNumber('staff')->name('staff.destroy');
    });
});
