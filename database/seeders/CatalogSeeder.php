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

        // Categories (align with seed data)
        $health = Category::firstOrCreate(['name' => 'Salud']);
        $life = Category::firstOrCreate(['name' => 'Vida']);
        $property = Category::firstOrCreate(['name' => 'Propiedad y Accidentes']);
        $business = Category::firstOrCreate(['name' => 'Empresarial']);
        $personal = Category::firstOrCreate(['name' => 'Personal']);
        $administrative = Category::firstOrCreate(['name' => 'Administrativo']);

        // 1. Seguros de Salud
        Offering::updateOrCreate(
            ['name' => 'Seguros de Salud'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $health->id,
                'type' => 'service',
                'description' => 'Cobertura médica integral para individuos y familias.',
                'commission_rate' => 30.00,
                'is_active' => true,
                'commission_config' => [
                    'monthly' => [
                        'percentage' => 30,
                        'amount' => null,
                        'duration_months' => null,
                    ],
                ],
                'commission_rules' => [
                    [
                        'condition' => 'default',
                        'commission_rate' => 30,
                        'label' => 'Salud mensual',
                        'roles' => ['associate'],
                    ],
                ],
            ]
        );

        // 2. Seguros de Vida
        Offering::updateOrCreate(
            ['name' => 'Seguros de Vida'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $life->id,
                'type' => 'service',
                'description' => 'Protección financiera y tranquilidad para seres queridos.',
                'commission_rate' => null,
                'base_commission' => 25.00,
                'is_active' => true,
                'commission_rules' => [
                    [
                        'condition' => 'default',
                        'fixed' => 25,
                        'label' => 'Vida pago único',
                        'roles' => ['associate'],
                    ],
                ],
            ]
        );

        // 3. Seguros de Carro y Casa
        Offering::updateOrCreate(
            ['name' => 'Seguros de Carro y Casa'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $property->id,
                'type' => 'service',
                'description' => 'Protección para hogar y vehículo contra imprevistos.',
                'commission_rate' => null,
                'base_commission' => 25.00,
                'is_active' => true,
                'commission_rules' => [
                    [
                        'condition' => 'default',
                        'fixed' => 25,
                        'label' => 'Auto y casa',
                        'roles' => ['associate'],
                    ],
                ],
            ]
        );

        // 4. Group Insurance (5+ empleados)
        Offering::updateOrCreate(
            ['name' => 'Group Insurance (5+ empleados)'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Seguros colectivos para empresas y PyMEs.',
                'commission_rate' => null,
                'base_commission' => 50.00,
                'is_active' => true,
                'form_schema' => [
                    ['label' => 'Nombre de la empresa', 'type' => 'text', 'required' => true],
                    ['label' => 'Número de empleados', 'type' => 'number', 'required' => true],
                ],
                'commission_rules' => [
                    [
                        'condition' => 'default',
                        'fixed' => 50,
                        'label' => 'Group Insurance',
                        'roles' => ['associate'],
                    ],
                ],
            ]
        );

        // 5. Business Liability / Workers Comp
        Offering::updateOrCreate(
            ['name' => 'Business Liability / Workers Comp'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Responsabilidad civil y compensación laboral para negocios.',
                'commission_rate' => 10.00,
                'is_active' => true,
                'commission_rules' => [
                    [
                        'condition' => 'default',
                        'commission_rate' => 10,
                        'label' => 'Liability',
                        'roles' => ['associate'],
                    ],
                ],
            ]
        );

        // 6. Taxes Personales
        Offering::updateOrCreate(
            ['name' => 'Taxes Personales'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $personal->id,
                'type' => 'service',
                'description' => 'Preparación y presentación de impuestos personales.',
                'commission_rate' => null,
                'base_commission' => 25.00,
                'is_active' => true,
                'form_schema' => [
                    ['label' => 'Año fiscal', 'type' => 'number', 'required' => true],
                    ['label' => 'Estado civil', 'type' => 'select', 'required' => true, 'options' => 'Soltero, Casado, Cabeza de hogar'],
                ],
                'commission_rules' => [
                    [
                        'condition' => 'default',
                        'fixed' => 25,
                        'label' => 'Taxes personales',
                        'roles' => ['associate'],
                    ],
                ],
            ]
        );

        // 7. Taxes Corporativos
        Offering::updateOrCreate(
            ['name' => 'Taxes Corporativos'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Servicios fiscales para corporaciones y negocios.',
                'commission_rate' => null,
                'base_commission' => 50.00,
                'is_active' => true,
                'form_schema' => [
                    ['label' => 'Nombre corporación', 'type' => 'text', 'required' => true],
                    ['label' => 'EIN', 'type' => 'text', 'required' => true],
                    ['label' => 'Tipo de entidad', 'type' => 'select', 'required' => true, 'options' => 'LLC, S-Corp, C-Corp, Partnership'],
                ],
                'commission_rules' => [
                    [
                        'condition' => 'default',
                        'fixed' => 50,
                        'label' => 'Taxes corporativos',
                        'roles' => ['associate'],
                    ],
                ],
            ]
        );

        // 8. Solicitud de Certificado (CDI)
        Offering::updateOrCreate(
            ['name' => 'Solicitud de Certificado (CDI)'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $administrative->id,
                'type' => 'service',
                'description' => 'Gestión de documentos y certificados oficiales (Liability, Workers Comp, etc.).',
                'commission_rate' => null,
                'base_commission' => 0.00,
                'is_active' => true,
                'form_schema' => [
                    ['label' => 'Titular', 'type' => 'text', 'required' => true],
                    ['label' => 'Asegurado', 'type' => 'text', 'required' => true],
                    ['label' => 'Tipo de certificado', 'type' => 'select', 'required' => true, 'options' => 'Liability, Workers Comp, Otros'],
                    ['label' => 'Fecha requerida', 'type' => 'date', 'required' => true],
                    ['label' => 'Dirección de envío', 'type' => 'text', 'required' => true],
                ],
            ]
        );
    }
}
