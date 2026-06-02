<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourPackage;

class TourPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name'          => 'Boracay Island Escape',
                'description'   => 'Enjoy the white sandy beaches and crystal-clear waters of Boracay. Includes hotel accommodation, island hopping, and beach activities.',
                'location'      => 'Boracay, Aklan',
                'price'         => 4500.00,
                'duration_days' => 3,
                'max_guests'    => 12,
                'rating'        => 4.9,
                'status'        => 'active',
            ],
            [
                'name'          => 'Manila City Tour',
                'description'   => 'Explore the historic Intramuros, Rizal Park, and the vibrant streets of Manila with a licensed tour guide.',
                'location'      => 'Manila, Metro Manila',
                'price'         => 1200.00,
                'duration_days' => 1,
                'max_guests'    => 20,
                'rating'        => 4.3,
                'status'        => 'active',
            ],
            [
                'name'          => 'Banaue Rice Terraces',
                'description'   => 'Trek through the 2,000-year-old rice terraces carved into the Cordillera mountains. A UNESCO World Heritage experience.',
                'location'      => 'Banaue, Ifugao',
                'price'         => 3800.00,
                'duration_days' => 2,
                'max_guests'    => 8,
                'rating'        => 4.8,
                'status'        => 'active',
            ],
            [
                'name'          => 'Palawan Underground River',
                'description'   => 'Discover the Puerto Princesa Subterranean River, one of the New 7 Wonders of Nature. Includes boat tour and lunch.',
                'location'      => 'Puerto Princesa, Palawan',
                'price'         => 5200.00,
                'duration_days' => 2,
                'max_guests'    => 10,
                'rating'        => 5.0,
                'status'        => 'active',
            ],
            [
                'name'          => 'Siargao Surf & Chill',
                'description'   => 'Learn to surf at Cloud 9 and explore the beautiful lagoons and islands around Siargao with local guides.',
                'location'      => 'Siargao, Surigao del Norte',
                'price'         => 6000.00,
                'duration_days' => 4,
                'max_guests'    => 6,
                'rating'        => 4.7,
                'status'        => 'active',
            ],
        ];

        foreach ($packages as $package) {
            TourPackage::updateOrCreate(
                ['name' => $package['name']],
                $package
            );
        }
    }
}
