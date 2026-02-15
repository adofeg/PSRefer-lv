<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $psadmin = User::role('psadmin')->first();
        $ownerEmployeeId = $psadmin?->profileable?->id;
        if (! $psadmin || ! $ownerEmployeeId) {
            return;
        }

        // Categories
        $health = Category::firstOrCreate(['name' => 'Salud']);
        $life = Category::firstOrCreate(['name' => 'Vida']);
        $property = Category::firstOrCreate(['name' => 'Propiedad y Accidentes']);
        $business = Category::firstOrCreate(['name' => 'Empresarial']);
        $personal = Category::firstOrCreate(['name' => 'Personal']);
        $administrative = Category::firstOrCreate(['name' => 'Administrativo']);

        // Helper to prepend identity fields to a schema
        $withIdentity = function($groups) {
            $systemFields = [
                ['name' => 'client_name', 'label' => 'Nombre Completo', 'type' => 'text', 'required' => true, 'is_system' => true],
                ['name' => 'client_email', 'label' => 'Correo Electrónico', 'type' => 'email', 'required' => true, 'is_system' => true],
                ['name' => 'client_phone', 'label' => 'Teléfono', 'type' => 'tel', 'required' => true, 'is_system' => true],
            ];

            if (empty($groups)) {
                $groups = [[
                    'id' => 'group_identity',
                    'title' => 'Datos Personales',
                    'fields' => $systemFields
                ]];
            } else {
                $groups[0]['fields'] = array_merge($systemFields, $groups[0]['fields']);
                // Ensure first group title makes sense for identity if it's generic
                if ($groups[0]['title'] === 'Datos del Servicio' || $groups[0]['title'] === 'Información General') {
                    $groups[0]['title'] = 'Datos Personales';
                }
            }

            return ['version' => 2, 'groups' => $groups];
        };

        // --- 1. SEGUROS DE SALUD ---
        Offering::updateOrCreate(
            ['name' => 'Seguros de Salud'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $health->id,
                'type' => 'service',
                'description' => 'Cobertura médica integral. Comisión del 30% mensual.',
                'commission_type' => 'percentage',
                'base_commission' => 30.00,
                'is_active' => true,
                'form_schema' => $withIdentity([
                    [
                        'id' => 'group_extra',
                        'title' => 'Datos del Servicio',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                        ]
                    ]
                ]),
            ]
        );

        // --- 2. SEGUROS DE VIDA ---
        Offering::updateOrCreate(
            ['name' => 'Seguros de Vida'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $life->id,
                'type' => 'service',
                'description' => 'Protección financiera. Comisión fija de $25.',
                'commission_type' => 'fixed',
                'base_commission' => 25.00, 
                'is_active' => true,
                'form_schema' => $withIdentity([
                    [
                        'id' => 'group_extra',
                        'title' => 'Datos del Servicio',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                        ]
                    ]
                ]),
            ]
        );

        // --- 3. SEGUROS DE CARRO Y CASA ---
        Offering::updateOrCreate(
            ['name' => 'Seguros de Carro y Casa'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $property->id,
                'type' => 'service',
                'description' => 'Seguros de auto (Personal/Comercial) y hogar. Comisión fija de $25.',
                'commission_type' => 'fixed',
                'base_commission' => 25.00, 
                'is_active' => true,
                'form_schema' => $withIdentity([
                    [
                        'id' => 'group_general',
                        'title' => 'Ubicación y Tipo',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                            ['name' => 'coverage_type', 'label' => 'Tipo de Seguro Requerido', 'type' => 'select', 'options' => 'Auto Personal, Auto Comercial, Casa, Múltiple', 'required' => true],
                            ['name' => 'current_address', 'label' => 'Dirección Actual Completa', 'type' => 'textarea', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 'group_auto',
                        'title' => 'Cuestionario de Auto (Personal / Comercial)',
                        'fields' => [
                            ['name' => 'business_name_auto', 'label' => 'Nombre Comercial (Si es empresa)', 'type' => 'text', 'required' => false],
                            ['name' => 'drivers_list', 'label' => 'Nombre de todos los potenciales conductores', 'type' => 'textarea', 'required' => false],
                            ['name' => 'household_members_15', 'label' => 'Mayores de 15 años que viven en la propiedad', 'type' => 'textarea', 'required' => false],
                            ['name' => 'vin_numbers', 'label' => 'Número(s) VIN de los autos', 'type' => 'textarea', 'required' => false],
                            ['name' => 'dot_number', 'label' => 'Número de DOT (Si corresponde)', 'type' => 'text', 'required' => false],
                            ['name' => 'cargo_type', 'label' => 'Tipo de Carga a Transportar', 'type' => 'text', 'required' => false],
                            ['name' => 'out_of_state', 'label' => '¿Se transporta fuera del estado?', 'type' => 'select', 'options' => 'Si, No, No Aplica', 'required' => false],
                            ['name' => 'claims_36m', 'label' => '¿Reclamos PIP o Accidentes (últimos 36 meses)?', 'type' => 'text', 'required' => false],
                            ['name' => 'coverage_details', 'label' => 'Tipo de Cobertura Necesaria / ¿Financiado?', 'type' => 'textarea', 'required' => false],
                        ]
                    ],
                    [
                        'id' => 'group_house',
                        'title' => 'Detalles de Casa (Si aplica)',
                        'fields' => [
                            ['name' => 'house_address', 'label' => 'Dirección de la Propiedad (Si es distinta)', 'type' => 'text', 'required' => false],
                            ['name' => 'construction_year', 'label' => 'Año de Construcción', 'type' => 'number', 'required' => false],
                            ['name' => 'roof_year', 'label' => 'Año del Techo', 'type' => 'number', 'required' => false],
                        ]
                    ],
                    [
                        'id' => 'group_documents',
                        'title' => 'Documentación Requerida (Archivos)',
                        'fields' => [
                            ['name' => 'declaration_page', 'label' => 'Declaration Page Vigente', 'type' => 'file', 'required' => false],
                            ['name' => 'driver_licenses', 'label' => 'Licencia de conducir de todos los conductores', 'type' => 'file', 'required' => false],
                            ['name' => 'car_photos_or_reg', 'label' => 'Fotos Millas/Lados O Matrícula/Registración', 'type' => 'file', 'required' => false],
                        ]
                    ]
                ]),
            ]
        );

        // --- 4. GROUP INSURANCE (5 EMPLEADOS O MAS) ---
        Offering::updateOrCreate(
            ['name' => 'Group Insurance (5 empleados o mas)'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Seguros colectivos comerciales. Comisión fija de $50.',
                'commission_type' => 'fixed',
                'base_commission' => 50.00, 
                'is_active' => true,
                'form_schema' => $withIdentity([
                    [
                        'id' => 'group_business',
                        'title' => 'Datos de la Empresa',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                            ['name' => 'business_name', 'label' => 'Nombre de la Empresa', 'type' => 'text', 'required' => true],
                            ['name' => 'employee_count', 'label' => 'Número de Empleados', 'type' => 'number', 'required' => true, 'min' => 5],
                            ['name' => 'census_file', 'label' => 'Censo de Empleados (PDF/Excel)', 'type' => 'file', 'required' => false],
                        ]
                    ]
                ]),
            ]
        );

        // --- 5. BUSINESS LIABILITY O WORKER'S COMP ---
        Offering::updateOrCreate(
            ['name' => "Business liability o Worker's Comp"],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Seguros empresariales (GL, WC, E&O). Comisión del 10%.',
                'commission_type' => 'percentage',
                'base_commission' => 10.00,
                'is_active' => true,
                'form_schema' => $withIdentity([
                    [
                        'id' => 'group_entity',
                        'title' => 'Entidad Legal',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                            ['name' => 'business_name', 'label' => 'Nombre de la Empresa', 'type' => 'text', 'required' => true],
                            ['name' => 'ein', 'label' => 'E.I.N.', 'type' => 'text', 'required' => true],
                            ['name' => 'entity_type', 'label' => 'Tipo de Entidad', 'type' => 'select', 'options' => 'Individual, L.L.C, Otra', 'required' => true],
                            ['name' => 'business_address', 'label' => 'Dirección, Ciudad, Zip Code', 'type' => 'textarea', 'required' => true],
                            ['name' => 'business_contact_info', 'label' => 'Teléfono y Email de la Empresa (Adicional)', 'type' => 'text', 'required' => false],
                        ]
                    ],
                    [
                        'id' => 'group_owners',
                        'title' => 'Dueños y Actividad',
                        'fields' => [
                            ['name' => 'owner_count', 'label' => 'Cuantos Dueños', 'type' => 'number', 'required' => true],
                            ['name' => 'owner_names', 'label' => 'Nombre de los Dueño(s)', 'type' => 'textarea', 'required' => true],
                            ['name' => 'owner_dob', 'label' => 'Fecha de Nacimiento de Dueño(s)', 'type' => 'text', 'required' => true],
                        ]
                    ],
                    [
                        'id' => 'group_general_biz',
                        'title' => 'Información Operativa',
                        'fields' => [
                            ['name' => 'business_activity', 'label' => 'Actividad de la Empresa / Ocupación', 'type' => 'textarea', 'required' => true],
                            ['name' => 'opening_date', 'label' => 'Fecha de Apertura / Años operando', 'type' => 'text', 'required' => true],
                            ['name' => 'annual_sales', 'label' => 'Ventas Anuales Proyectadas (Próximos 12 meses)', 'type' => 'number', 'required' => true],
                            ['name' => 'employee_count', 'label' => 'Número de Empleados', 'type' => 'number', 'required' => true],
                            ['name' => 'has_other_branches', 'label' => '¿Tiene otras sucursales?', 'type' => 'select', 'options' => 'Si, No', 'required' => false],
                            ['name' => 'partner_count', 'label' => 'Número de Socios', 'type' => 'number', 'required' => false],
                        ]
                    ],
                    [
                        'id' => 'group_eo_details',
                        'title' => 'Detalles de Riesgo y E&O',
                        'fields' => [
                            ['name' => 'subcontractor_pay_12m', 'label' => 'Pago a sub-contratistas (Próximos 12 meses)', 'type' => 'number', 'required' => false],
                            ['name' => 'has_current_eo', 'label' => '¿Tiene ahora un E&O?', 'type' => 'select', 'options' => 'Si, No', 'required' => false],
                            ['name' => 'effective_date', 'label' => 'Fecha de Efectividad deseada', 'type' => 'date', 'required' => false],
                            ['name' => 'sunbiz_registration', 'label' => 'Copia del EIN o Registro SunBiz (Archivo)', 'type' => 'file', 'required' => false],
                        ]
                    ],
                    [
                        'id' => 'group_payment',
                        'title' => 'Información de Pago (Opcional)',
                        'fields' => [
                            ['name' => 'card_name', 'label' => 'Nombre en Tarjeta', 'type' => 'text', 'required' => false],
                            ['name' => 'card_number', 'label' => 'Número de Tarjeta', 'type' => 'text', 'required' => false],
                            ['name' => 'card_exp_cvc', 'label' => 'Exp / CVC', 'type' => 'text', 'required' => false],
                        ]
                    ]
                ]),
            ]
        );

        // --- 6. TAXES PERSONALES ---
        Offering::updateOrCreate(
            ['name' => 'Taxes personales'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $personal->id,
                'type' => 'service',
                'description' => 'Preparación de impuestos personales. Comisión fija de $25.',
                'commission_type' => 'fixed',
                'base_commission' => 25.00, 
                'is_active' => true,
                'form_schema' => $withIdentity([
                    [
                        'id' => 'group_extra',
                        'title' => 'Datos del Servicio',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                        ]
                    ]
                ]),
            ]
        );

        // --- 7. TAXES CORPORATIVOS ---
        Offering::updateOrCreate(
            ['name' => 'Taxes corporativos'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Impuestos para empresas. Comisión fija de $50.',
                'commission_type' => 'fixed',
                'base_commission' => 50.00, 
                'is_active' => true,
                'form_schema' => $withIdentity([
                    [
                        'id' => 'group_biz',
                        'title' => 'Datos de la Empresa',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                            ['name' => 'business_name', 'label' => 'Nombre de la Empresa', 'type' => 'text', 'required' => true],
                        ]
                    ]
                ]),
            ]
        );

        // Inactive / Admin
        Offering::updateOrCreate(
            ['name' => 'Pedir Certificado de Seguro (CDI)'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $administrative->id,
                'type' => 'service',
                'description' => 'Servicio para clientes existentes.',
                'base_commission' => 0.00,
                'is_active' => false,
                'form_schema' => ['version' => 2, 'groups' => []]
            ]
        );
    }
}
