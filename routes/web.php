<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
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

Route::get('/reservations', [DashboardController::class, 'reservations'])
    ->middleware('auth')
    ->name('reservations.index');

Route::post('/bookings', [DashboardController::class, 'storeBooking'])
    ->middleware('auth')
    ->name('bookings.store');

Route::get('/admin/dashboard', [\App\Http\Controllers\DashboardController::class, 'admin'])
    ->middleware(['auth:admin', 'admin'])
    ->name('admin.dashboard');

Route::get('/admin/reports/bookings/{format?}', [ReportController::class, 'bookings'])
    ->middleware(['auth:admin', 'admin'])
    ->whereIn('format', ['json', 'csv', 'xlsx', 'pdf'])
    ->name('admin.reports.bookings');
