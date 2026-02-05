<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Tecnología', 'description' => 'Servicios y productos tecnológicos'],
            ['name' => 'Salud', 'description' => 'Servicios de salud y bienestar'],
            ['name' => 'Hogar', 'description' => 'Servicios para el hogar y mantenimiento'],
            ['name' => 'Finanzas', 'description' => 'Servicios financieros y contables'],
            ['name' => 'Marketing', 'description' => 'Servicios de marketing y publicidad']
        ];

        foreach ($categories as $category) {
            \App\Models\Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
