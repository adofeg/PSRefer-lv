<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. PS Admin (Super Admin)
        $psadmin = User::firstOrCreate(
            ['email' => 'psadmin@psrefer.com'],
            [
                'name' => 'PS Administrator',
                'password' => Hash::make('password'),
                'role' => 'psadmin', // Legacy column
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $psadmin->assignRole('psadmin');

        // 2. Admin (Standard Manager)
        $admin = User::firstOrCreate(
            ['email' => 'admin@psrefer.com'],
            [
                'name' => 'System Manager',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // 3. Associate (Partner) 1
        $associate = User::firstOrCreate(
            ['email' => 'partner@psrefer.com'],
            [
                'name' => 'Active Partner',
                'password' => Hash::make('password'),
                'role' => 'associate',
                'is_active' => true,
                'email_verified_at' => now(),
                'balance' => 0.00,
            ]
        );
        $associate->assignRole('associate');

        // 4. Generate Random Associates with Data
        User::factory(5)->create()->each(function ($user) {
            $user->assignRole('associate');
        });
    }
}
