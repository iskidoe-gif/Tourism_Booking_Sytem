<?php

use App\Models\Booking;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the application returns a successful response', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

test('a tour can be booked in demo mode', function () {
    $user = User::create([
        'name' => 'Test Tourist',
        'email' => 'tourist.test@example.com',
        'password' => bcrypt('password'),
        'role' => 'tourist',
    ]);

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

    $response = $this->actingAs($user)->post(route('bookings.store'), [
        'tour_package_id' => $package->id,
        'tour_date' => now()->addDays(3)->format('Y-m-d'),
        'num_guests' => 2,
        'special_requests' => 'Vegetarian meals',
    ]);

    $response->assertRedirect();
    expect(Booking::count())->toBe(1);
    expect(Booking::first()->user_id)->toBe($user->id);
});
