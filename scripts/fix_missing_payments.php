<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Models\Payment;

echo "Fixing missing payment records...\n";

$bookingsWithoutPayments = Booking::whereDoesntHave('payment')->get();

echo "Found {$bookingsWithoutPayments->count()} bookings without payment records.\n";

foreach ($bookingsWithoutPayments as $booking) {
    $payment = $booking->payment()->create([
        'amount' => $booking->total_price,
        'status' => 'unpaid',
        'method' => 'cash',
    ]);

    echo "Created payment record for booking #{$booking->booking_number} (Payment ID: {$payment->id})\n";
}

echo "\nDone! All bookings now have payment records.\n";
