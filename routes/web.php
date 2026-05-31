<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tourist\PackageController;
use App\Http\Controllers\Tourist\BookingController;
use App\Http\Controllers\Tourist\ReservationController;

require __DIR__.'/auth.php';

// Public: browse packages
Route::get('/', [PackageController::class, 'index'])->name('home');
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/{tourPackage}', [PackageController::class, 'show'])->name('packages.show');

// Demo booking flow
Route::get('/book/{tourPackage}', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/book/{tourPackage}', [BookingController::class, 'store'])->name('bookings.store');

// My Reservations
Route::get('/my-reservations', [ReservationController::class, 'index'])->name('reservations.index');
Route::get('/my-reservations/{booking}', [ReservationController::class, 'show'])->name('reservations.show');
Route::delete('/my-reservations/{booking}', [ReservationController::class, 'cancel'])->name('reservations.cancel');
