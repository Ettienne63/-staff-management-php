<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\InternalSchoolController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SchoolOnboardingController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Middleware\EnsurePlatformAdmin;
use App\Http\Middleware\EnsureSchoolAccess;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [AuthController::class, 'create'])->name('login');
    Route::get('/login', fn () => redirect()->route('login'));
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
});
Route::get('/invitations/{token}', [InvitationController::class, 'show'])->where('token', '[A-Za-z0-9]{64}')
    ->middleware('throttle:30,1')->name('invitations.show');
Route::post('/invitations/{token}', [InvitationController::class, 'accept'])->where('token', '[A-Za-z0-9]{64}')
    ->middleware('throttle:10,1')->name('invitations.accept');
Route::middleware('auth')->group(function (): void {
    Route::prefix('internal')->name('internal.')->middleware(EnsurePlatformAdmin::class)->group(function (): void {
        Route::get('/schools', [InternalSchoolController::class, 'index'])->name('schools.index');
        Route::get('/schools/create', [InternalSchoolController::class, 'create'])->name('schools.create');
        Route::post('/schools', [InternalSchoolController::class, 'store'])->name('schools.store');
    });
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [SchoolController::class, 'index'])->name('dashboard');
    Route::post('/family/requests', [FamilyController::class, 'store'])->middleware('throttle:10,1')->name('family.requests.store');
    Route::get('/family/children/{student}', [FamilyController::class, 'show'])->whereNumber('student')->name('family.children.show');
    Route::get('/family/{section?}', [FamilyController::class, 'index'])
        ->whereIn('section', ['progress', 'events'])->name('family.dashboard');
    Route::redirect('/admin/dashboard', '/dashboard');
    Route::get('/admin/staff', [SchoolController::class, 'legacyStaff']);
    Route::prefix('schools/{school}')->middleware(EnsureSchoolAccess::class)->group(function (): void {
        Route::post('/parents/invite', [SchoolOnboardingController::class, 'inviteGuardian'])->middleware('throttle:10,1')->name('onboarding.guardians.invite');
        Route::post('/child-requests/{registration}/review', [SchoolOnboardingController::class, 'review'])->whereNumber('registration')->name('onboarding.requests.review');
        Route::post('/students/{student}/invite', [SchoolOnboardingController::class, 'inviteStudent'])->whereNumber('student')->middleware('throttle:10,1')->name('onboarding.students.invite');
        Route::delete('/students/{student}/guardians/{guardian}', [SchoolOnboardingController::class, 'revokeGuardian'])->whereNumber(['student', 'guardian'])->name('onboarding.guardians.revoke');
        Route::delete('/invitations/{invitation}', [SchoolOnboardingController::class, 'revokeInvitation'])->whereNumber('invitation')->name('onboarding.invitations.revoke');
        Route::get('/', [WorkspaceController::class, 'index'])->name('schools.workspace');
        Route::get('/workspace/{section}', [WorkspaceController::class, 'section'])
            ->whereIn('section', ['accounts', 'students', 'training', 'progress'])->name('schools.section');
        Route::post('/select', [SchoolController::class, 'select'])->name('schools.select');
        Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->whereNumber('staff')->name('staff.edit');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->whereNumber('staff')->name('staff.update');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->whereNumber('staff')->name('staff.destroy');
    });
});
