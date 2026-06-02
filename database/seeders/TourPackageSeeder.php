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
                'image'         => 'images/hundredislanddaytour.png',
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
                'image'         => 'images/hundredislanddaytour.png',
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
                'image'         => 'images/hundredislanddaytour.png',
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
                'image'         => 'images/hundredislanddaytour.png',
                'rating'        => 4.8,
                'status'        => 'active',
            ],
            [
                'name'          => 'Poro Point Lighthouse Adventure',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Explore the iconic Poro Point Lighthouse with panoramic coastal views and colonial architecture.',
                'price'         => 1900.00,
                'duration_days' => 1,
                'max_guests'    => 20,
                'image'         => 'images/hundredislanddaytour.png',
                'rating'        => 4.6,
                'status'        => 'active',
            ],
            [
                'name'          => 'Bolinao Waterfall Trekking',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Adventure through lush forests to discover hidden waterfalls and natural pools in Bolinao.',
                'price'         => 2200.00,
                'duration_days' => 1,
                'max_guests'    => 14,
                'image'         => 'images/hundredislanddaytour.png',
                'rating'        => 4.7,
                'status'        => 'active',
            ],
            [
                'name'          => 'Coastal Village Tour & Fishing Experience',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Experience authentic local life with traditional fishing methods and visits to charming coastal villages.',
                'price'         => 2400.00,
                'duration_days' => 1,
                'max_guests'    => 10,
                'image'         => 'images/hundredislanddaytour.png',
                'rating'        => 4.5,
                'status'        => 'active',
            ],
            [
                'name'          => 'Bolinao Sunset Cruise & Dinner',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Romantic sunset cruise along the Bolinao coast with fresh seafood dinner and live entertainment.',
                'price'         => 3800.00,
                'duration_days' => 1,
                'max_guests'    => 30,
                'image'         => 'images/hundredislanddaytour.png',
                'rating'        => 4.9,
                'status'        => 'active',
            ],
            [
                'name'          => 'Island Camping & Stargazing',
                'location'      => 'Hundred Islands, Pangasinan',
                'description'   => 'Overnight camping on pristine island with bonfire, stargazing, and beach activities.',
                'price'         => 4500.00,
                'duration_days' => 2,
                'max_guests'    => 15,
                'image'         => 'images/hundredislanddaytour.png',
                'rating'        => 4.8,
                'status'        => 'active',
            ],
            [
                'name'          => 'Marine Life Discovery & Diving',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Guided snorkeling and diving tours to explore coral reefs and diverse marine life around Bolinao.',
                'price'         => 3500.00,
                'duration_days' => 1,
                'max_guests'    => 12,
                'image'         => 'images/hundredislanddaytour.png',
                'rating'        => 4.7,
                'status'        => 'active',
            ],
            [
                'name'          => 'Heritage & Culture Museum Tour',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Deep dive into Bolinao\'s rich history with guided tours of museums and historical landmarks.',
                'price'         => 1600.00,
                'duration_days' => 1,
                'max_guests'    => 25,
                'image'         => 'images/hundredislanddaytour.png',
                'rating'        => 4.4,
                'status'        => 'active',
            ],
            [
                'name'          => 'Wellness Retreat & Beach Spa',
                'location'      => 'Bolinao, Pangasinan',
                'description'   => 'Relaxing spa treatments, yoga sessions, and wellness activities on the beach with ocean views.',
                'price'         => 3900.00,
                'duration_days' => 1,
                'max_guests'    => 20,
                'image'         => 'images/hundredislanddaytour.png',
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
