<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Admin;
use App\Models\Destination;
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
        // Seed destinations first
        $this->call(DestinationSeeder::class);

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

        $this->call(ReviewSeeder::class);

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
                'num_adults' => 2,
                'num_children' => 0,
                'num_seniors' => 0,
                'status' => 'confirmed',
                'base_price' => 4500,
                'additional_fees' => 0,
                'discount_amount' => 0,
                'total_price' => 9000,
                'confirmation_code' => 'CONF-' . Str::upper(Str::random(10)),
                'reference_code' => 'REF-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'special_requests' => 'Seeded booking.',
                'payment_plan' => 'full',
                'payment_installments' => 1,
                'guest_details' => json_encode([
                    [
                        'name' => 'Juan Dela Cruz',
                        'email' => 'juan@example.com',
                        'phone' => '+639123456789',
                        'age' => 35,
                    ],
                    [
                        'name' => 'Maria Dela Cruz',
                        'email' => 'maria@example.com',
                        'phone' => '+639987654321',
                        'age' => 32,
                    ],
                ]),
                'services' => json_encode([
                    [
                        'name' => 'Airport Pickup',
                        'price' => 500,
                        'description' => 'Hotel to airport transportation',
                    ],
                ]),
                'confirmed_at' => now(),
                'reminder_sent' => false,
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
