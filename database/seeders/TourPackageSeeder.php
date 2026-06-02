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
                'name'          => 'Bolinao Church & Cape Tour',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Visit the historic Bolinao Church, Cape Bolinao Lighthouse, and the scenic seaside cliffs of the northwest coast.',
                'price'         => 2800.00,
                'duration_days' => 1,
                'max_guests'    => 16,
                'image'         => 'images/bolinao-church.jpg',
                'rating'        => 4.9,
                'status'        => 'active',
            ],
            [
                'name'          => 'Patar Beach Sunrise Escape',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Relax on Patar Beach, swim in clear waters, and catch the sunrise over the famous white sand shore.',
                'price'         => 2600.00,
                'duration_days' => 1,
                'max_guests'    => 12,
                'image'         => 'images/patar-beach.jpg',
                'rating'        => 4.7,
                'status'        => 'active',
            ],
            [
                'name'          => 'Enchanted Cave & Shell Museum',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Swim in the Enchanted Cave and explore the Shell Museum with its world-class seashell collection.',
                'price'         => 3200.00,
                'duration_days' => 1,
                'max_guests'    => 10,
                'image'         => 'images/enchanted-cave.jpg',
                'rating'        => 4.8,
                'status'        => 'active',
            ],
            [
                'name'          => 'Hundred Islands & Island Hopping',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Boat transfers to the Hundred Islands, snorkeling, and island hopping around the scenic Pangasinan archipelago.',
                'price'         => 3600.00,
                'duration_days' => 1,
                'max_guests'    => 18,
                'image'         => 'images/hundred-islands.jpg',
                'rating'        => 4.8,
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
