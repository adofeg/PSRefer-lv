<?php

namespace Database\Seeders;

use App\Models\Offering;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $psadmin = User::role('psadmin')->first();
        $ownerEmployeeId = $psadmin?->employeeProfile()?->id;
        if (!$psadmin || !$ownerEmployeeId) return;

        // Categories
        $marketing = Category::firstOrCreate(['name' => 'Marketing']);
        $medical = Category::firstOrCreate(['name' => 'Medical']);
        $commercial = Category::firstOrCreate(['name' => 'Comercial']);

        // 1. Referencia General
        Offering::firstOrCreate(
            ['name' => 'Referencia General'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $commercial->id,
                'type' => 'service',
                'description' => 'Enlace general para referir cualquier servicio. El equipo de PSRefer se encargará de clasificarlo.',
                'commission_rate' => 10.00,
                'is_active' => true,
                'form_schema' => [
                    [
                        'label' => 'Servicios de Interés',
                        'type' => 'checkbox_group',
                        'required' => true,
                        'options' => ['Marketing Digital', 'Consultoría Médica', 'Software a Medida', 'Otros']
                    ]
                ]
            ]
        );

        // 2. Marketing Digital
        Offering::firstOrCreate(
            ['name' => 'Marketing Digital'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $marketing->id,
                'type' => 'service',
                'description' => 'Servicios integrales de SEO, SEM y Redes Sociales.',
                'commission_rate' => 15.00,
                'is_active' => true,
                'form_schema' => [
                    ['label' => 'Presupuesto Mensual', 'type' => 'number', 'required' => true],
                    ['label' => 'Sitio Web Actual', 'type' => 'url', 'required' => false]
                ]
            ]
        );

        // 3. Consultoría Médica
        Offering::firstOrCreate(
            ['name' => 'Consultoría Médica'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $medical->id,
                'type' => 'service',
                'description' => 'Especialistas en habilitación de IPS y gestión de calidad en salud.',
                'commission_rate' => 12.50,
                'is_active' => true,
                'form_schema' => [
                    ['label' => 'Tipo de Entidad', 'type' => 'select', 'required' => true, 'options' => 'Consultorio,Clínica,Hospital']
                ]
            ]
        );
    }
}
