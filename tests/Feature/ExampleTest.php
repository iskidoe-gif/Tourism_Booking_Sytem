<?php

use App\Models\Booking;
use App\Models\TourPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('a tour can be booked in demo mode', function () {
    $package = TourPackage::create([
        'name' => 'Test Island Tour',
        'description' => 'A test tour package.',
        'location' => 'Test Location',
        'price' => 1500,
        'duration_days' => 2,
        'max_guests' => 4,
        'status' => 'active',
        'rating' => 4.5,
    ]);

    $response = $this->post(route('bookings.store', $package), [
        'tour_date' => now()->addDays(3)->format('Y-m-d'),
        'num_guests' => 2,
        'special_requests' => 'Vegetarian meals',
    ]);

    $response->assertRedirect();
    expect(Booking::count())->toBe(1);
});
