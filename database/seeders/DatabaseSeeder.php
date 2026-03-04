<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create default admin user (won't duplicate if already exists)
        \App\Models\User::firstOrCreate(
            ['email' => 'opollydo@gmail.com'],
            [
                'name' => 'OPOL LYDO',
                'password' => 'opolyouth', // will be hashed by User cast
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
