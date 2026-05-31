<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin account
        User::updateOrCreate(['email' => 'admin@tourph.com'], [
            'name'     => 'Admin User',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        User::updateOrCreate(['email' => 'juan@example.com'], [
            'name'     => 'Juan Dela Cruz',
            'password' => Hash::make('password123'),
            'role'     => 'tourist',
        ]);
    }
}
