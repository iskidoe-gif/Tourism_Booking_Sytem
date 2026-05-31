<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Admin;
use App\Models\Payment;
use App\Models\TourPackage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminAccount = Admin::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory()->create([
            'name' => 'Tourist Creator',
            'email' => 'creator@example.com',
            'password' => 'password',
            'role' => 'tourist',
        ]);

        User::factory()->create([
            'name' => 'Tourist User',
            'email' => 'tourist@example.com',
            'password' => 'password',
            'role' => 'tourist',
        ]);

        $packages = collect([
            [
                'title' => 'Bohol Countryside Escape',
                'destination' => 'Bohol',
                'description' => 'Chocolate Hills, tarsiers, and river cruising.',
                'price' => 4500,
                'duration_days' => 2,
                'max_guests' => 20,
            ],
            [
                'title' => 'Siargao Island Adventure',
                'destination' => 'Siargao',
                'description' => 'Surf lessons, island hopping, and lagoon stops.',
                'price' => 6800,
                'duration_days' => 3,
                'max_guests' => 15,
            ],
        ])->map(function (array $package) use ($adminAccount) {
            return TourPackage::create([
                ...$package,
                'slug' => Str::slug($package['title']),
                'created_by' => $adminAccount->id,
                'includes' => ['Guide', 'Van transfer', 'Lunch'],
                'itinerary' => ['Day 1: Arrival', 'Day 2: Tour'],
                'is_active' => true,
            ]);
        });

        $tourist = User::where('email', 'tourist@example.com')->first();
        $booking = Booking::create([
            'booking_code' => 'BK-' . now()->format('Ymd') . '-SEED1',
            'user_id' => $tourist->id,
            'tour_package_id' => $packages->first()->id,
            'booking_date' => now()->addWeek()->toDateString(),
            'guests' => 2,
            'status' => 'approved',
            'total_amount' => 9000,
            'approved_by' => $adminAccount->id,
            'approved_at' => now(),
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => 9000,
            'method' => 'gcash',
            'transaction_reference' => 'TRX-SEED-001',
            'status' => 'paid',
            'paid_at' => now(),
            'meta' => ['note' => 'Seeded payment'],
        ]);
    }
}
