<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PackageApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\PaymentApiController;

/*
|--------------------------------------------------------------------------
| REST API Routes — Bolinao Tourism Booking System
|--------------------------------------------------------------------------
| Base URL: /api
| Auth:     Sanctum token  →  pass  "Authorization: Bearer {token}"
*/

// ── Public ────────────────────────────────────────────────────
Route::get('/packages',          [PackageApiController::class, 'index']);   // GET  /api/packages
Route::get('/packages/{id}',     [PackageApiController::class, 'show']);    // GET  /api/packages/{id}

// ── Protected ─────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Bookings
    Route::get('/bookings',          [BookingApiController::class, 'index']);   // GET
    Route::post('/bookings',         [BookingApiController::class, 'store']);   // POST
    Route::get('/bookings/{id}',     [BookingApiController::class, 'show']);    // GET  /{id}
    Route::put('/bookings/{id}',     [BookingApiController::class, 'update']);  // PUT
    Route::delete('/bookings/{id}',  [BookingApiController::class, 'destroy']); // DELETE

    // Payments
    Route::get('/payments',          [PaymentApiController::class, 'index']);   // GET
    Route::post('/payments',         [PaymentApiController::class, 'store']);   // POST
    Route::get('/payments/{id}',     [PaymentApiController::class, 'show']);    // GET  /{id}
});
