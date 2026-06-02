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
        $adminAccount = Admin::updateOrCreate(
            ['email' => 'admin@tourph.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@tourph.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password123'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'juan@example.com'],
            [
                'name' => 'Juan Dela Cruz',
                'password' => bcrypt('password123'),
                'role' => 'tourist',
            ]
        );

        User::updateOrCreate(
            ['email' => 'tourist@example.com'],
            [
                'name' => 'Tourist User',
                'password' => bcrypt('password123'),
                'role' => 'tourist',
            ]
        );

        $this->call(TourPackageSeeder::class);

        $tourist = User::where('email', 'tourist@example.com')->first();
        $tourPackage = TourPackage::where('status', 'active')->first();

        if (! $tourPackage) {
            return;
        }

        $booking = Booking::updateOrCreate(
            ['booking_number' => 'BK-' . now()->format('Ymd') . '-SEED1'],
            [
                'user_id' => $tourist->id,
                'tour_package_id' => $tourPackage->id,
                'tour_date' => now()->addWeek()->toDateString(),
                'num_guests' => 2,
                'status' => 'confirmed',
                'total_price' => 9000,
                'special_requests' => 'Seeded booking.',
            ]
        );

        Payment::updateOrCreate(
            ['reference_number' => 'TRX-SEED-001'],
            [
                'booking_id' => $booking->id,
                'amount' => 9000,
                'method' => 'gcash',
                'status' => 'paid',
                'reference_number' => 'TRX-SEED-001',
                'paid_at' => now(),
            ]
        );
    }
}
