<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SKAccountsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with SK accounts for each barangay.
     */
    public function run(): void
    {
        $barangays = [
            'AWANG',
            'BAGOCBOC', 
            'BARRA',
            'BONBON',
            'CAUYUNAN',
            'IGPIT',
            'LIMUNDA',
            'LUYONG BONBON',
            'MALANANG',
            'NANGCAON',
            'PATAG',
            'POBLACION',
            'TABOC',
            'TINGALAN'
        ];

        foreach ($barangays as $barangay) {
            // Create SK account for each barangay
            User::firstOrCreate(
                ['email' => 'sk-' . strtolower(str_replace(' ', '', $barangay)) . '@opol.gov'],
                [
                    'name' => 'SK Chairman - ' . $barangay,
                    'password' => Hash::make('sk123456'), // Default password
                    'role' => 'sk',
                    'barangay' => $barangay,
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('SK accounts created for all barangays successfully!');
        $this->command->info('Default password: sk123456');
        $this->command->info('Email format: sk-{barangay}@opol.gov');
    }
}
