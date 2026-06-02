<?php

use Illuminate\Support\Facades\Route;

// Auth Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Tourist Controllers
use App\Http\Controllers\Tourist\PackageController;
use App\Http\Controllers\Tourist\BookingController;
use App\Http\Controllers\Tourist\ReservationController;
use App\Http\Controllers\Tourist\ReviewController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminPackageController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Middleware\AdminMiddleware;

// ── Home ──────────────────────────────────────────────────────
Route::view('/', 'home')->name('home');

// ── Auth (guests only) ────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [LoginController::class,    'showForm'])->name('login');
    Route::post('/login',   [LoginController::class,    'login']);
    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register',[RegisterController::class, 'register']);
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ── Tourist (auth required) ───────────────────────────────────
Route::middleware('auth')->group(function () {

    // Packages
    Route::get('/packages',             [PackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/{tourPackage}',[PackageController::class, 'show'])->name('packages.show');

    // Bookings
    Route::get('/book/{tourPackage}',   [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/book/{tourPackage}',  [BookingController::class, 'store'])->name('bookings.store');

    // My Reservations
    Route::get('/my-reservations',              [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/my-reservations/{booking}',    [ReservationController::class, 'show'])->name('reservations.show');
    Route::delete('/my-reservations/{booking}', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Reviews
    Route::post('/reviews/{booking}', [ReviewController::class, 'store'])->name('reviews.store');
});

// ── Admin (admin middleware) ───────────────────────────────────
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Package CRUD
    Route::resource('packages', AdminPackageController::class);

    // Booking management
    Route::get('/bookings',                       [AdminBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}',             [AdminBookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/confirm',   [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::patch('/bookings/{booking}/cancel',    [AdminBookingController::class, 'cancel'])->name('bookings.cancel');

    // Reports
    Route::get('/reports',               [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/pdf',    [ReportController::class, 'exportPdf'])->name('reports.pdf');
    Route::get('/reports/export/csv',    [ReportController::class, 'exportCsv'])->name('reports.csv');
    Route::get('/reports/export/xlsx',   [ReportController::class, 'exportXlsx'])->name('reports.xlsx');
    Route::get('/reports/export/json',   [ReportController::class, 'exportJson'])->name('reports.json');
});
