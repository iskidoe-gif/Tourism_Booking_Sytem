<?php

use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Tourist\ReservationController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    // Keep a lightweight GET route for legacy '/login' links.
    Route::get('/login', function () { return redirect()->route('home', ['auth' => 'signin']); })->name('login');
    Route::get('/register', function () { return redirect()->route('home', ['auth' => 'register']); });

    Route::post('/login', [AuthController::class, 'loginTourist'])->name('login.store');
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'loginAdmin'])->name('admin.login.store');
    Route::post('/guest-login', [AuthController::class, 'guestLogin'])->name('guest.login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/packages', [DashboardController::class, 'packages'])
    ->middleware('auth')
    ->name('packages.index');

Route::get('/reservations', [ReservationController::class, 'index'])
    ->middleware('auth')
    ->name('reservations.index');

Route::get('/my-reservations', function () {
    return redirect()->route('reservations.index');
})
    ->middleware('auth');

Route::get('/reservations/{booking}', [ReservationController::class, 'show'])
    ->middleware('auth')
    ->name('reservations.show');

Route::delete('/reservations/{booking}', [ReservationController::class, 'cancel'])
    ->middleware('auth')
    ->name('reservations.cancel');

Route::post('/bookings', [DashboardController::class, 'storeBooking'])
    ->middleware('auth')
    ->name('bookings.store');

Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
    ->middleware(['auth:admin', EnsureAdmin::class])
    ->name('admin.dashboard');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth:admin', EnsureAdmin::class])
    ->group(function () {
        Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
        Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
        Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
        Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

        Route::get('/reports/bookings/{format?}', [ReportController::class, 'bookings'])
            ->whereIn('format', ['json', 'csv', 'xlsx', 'pdf'])
            ->name('reports.bookings');
    });
