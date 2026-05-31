<?php

use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\TourPackageController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::apiResource('packages', TourPackageController::class);
Route::apiResource('bookings', BookingController::class);
Route::apiResource('payments', PaymentController::class);

Route::get('reports/bookings/{format?}', [ReportController::class, 'bookings'])
    ->whereIn('format', ['json', 'csv', 'xlsx', 'pdf']);
