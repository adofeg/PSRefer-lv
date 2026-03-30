<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sectors = [
            'Realtor',
            'Contador',
            'Agente de seguros',
        ];

        foreach ($sectors as $sector) {
            \App\Models\Sector::firstOrCreate(['name' => $sector]);
        }
    }
}
