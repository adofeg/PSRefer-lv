<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class, // Now contains Production Users
            CategorySeeder::class,
            CatalogSeeder::class,
            ReferralSeeder::class, // Now contains Production Referrals
            ActivityLogSeeder::class, // Now contains Production Logs
        ]);
    }
}
