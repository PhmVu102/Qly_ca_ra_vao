<?php
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ManageStaffController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\ScheduleController;

use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::middleware(['auth', 'check.locked'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('admin/schedules/get-assigned-users', [ScheduleController::class, 'getAssignedUsers'])
    ->middleware(['web', 'auth'])
    ->name('admin.schedules.get_assigned_users');

// Route admin
Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware(['auth', 'verified', 'check.locked'])->name('dashboard');

Route::prefix('admin')->middleware(['auth', 'role:admin', 'check.locked'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::resource('staff', App\Http\Controllers\Admin\ManageStaffController::class)
        ->names('admin.staff');

    Route::post('staff/{staff}/toggle-lock', [App\Http\Controllers\Admin\ManageStaffController::class, 'toggleLock'])
        ->name('admin.staff.toggle-lock');

    Route::resource('departments', App\Http\Controllers\Admin\DepartmentController::class)
        ->names('admin.departments');

    Route::resource('shifts', App\Http\Controllers\Admin\ShiftController::class)
        ->names('admin.shifts');

    Route::resource('schedules', App\Http\Controllers\Admin\ScheduleController::class)
        ->names('admin.schedules');

    Route::resource('attendance', App\Http\Controllers\Admin\AttendanceController::class)
        ->names('admin.attendance');

    Route::resource('locations', App\Http\Controllers\Admin\LocationController::class)
        ->names('admin.locations');
});

// Route staff
Route::middleware(['auth', 'role:staff', 'check.locked'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffController::class, 'dashboard'])
        ->name('dashboard');

    Route::post('/check-in', [StaffController::class, 'checkIn'])
        ->name('checkin');

    Route::post('/check-out', [StaffController::class, 'checkOut'])
        ->name('checkout');

    Route::get('/history', [StaffController::class, 'history'])
        ->name('history');

    Route::get('/profile', [StaffController::class, 'profile'])
        ->name('profile');

    Route::patch('/update', [StaffController::class, 'updateProfile'])
        ->name('update');

    Route::get('/current-shift', [StaffController::class, 'currentShift'])
        ->name('currentShift');

    Route::patch('/change-password', [StaffController::class, 'changePassword'])
        ->name('changePassword');

    Route::post('/check-password', [StaffController::class, 'checkCurrentPassword'])
        ->name('checkPassword');
});
