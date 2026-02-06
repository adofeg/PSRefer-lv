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
            ['name' => 'Salud', 'description' => 'Servicios de salud y bienestar'],
            ['name' => 'Vida', 'description' => 'Protección financiera y seguros de vida'],
            ['name' => 'Propiedad y Accidentes', 'description' => 'Coberturas de auto, casa y propiedad'],
            ['name' => 'Empresarial', 'description' => 'Servicios y coberturas para empresas'],
            ['name' => 'Personal', 'description' => 'Servicios personales y particulares'],
            ['name' => 'Administrativo', 'description' => 'Trámites y gestión documental'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
