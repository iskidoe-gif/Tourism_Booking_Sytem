<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TourPackageController;
use Illuminate\Support\Facades\Route;

Route::get('/packages', [TourPackageController::class, 'index']);
Route::get('/packages/{package}', [TourPackageController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('payments', PaymentController::class)->only([
        'index',
        'store',
        'show',
    ]);
});
