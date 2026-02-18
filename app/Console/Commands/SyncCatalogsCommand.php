<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Offering;
use App\Models\User;

class SyncCatalogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'psrefer:sync-catalogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncs the Offerings catalog in production with the latest schema definitions (v2.0)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Catalog Sync...');

        // 1. Ensure Owner Exists
        $psadmin = User::role('psadmin')->first();
        if (!$psadmin || !$psadmin->profileable) {
            $this->error('User with role "psadmin" not found. Cannot assign owner.');
            return 1;
        }
        $ownerEmployeeId = $psadmin->profileable->id;
        $this->info("Owner identified: {$psadmin->name} (Employee ID: $ownerEmployeeId)");

        // 2. Ensure Categories Exist
        $categories = [
            'Salud' => 'Salud',
            'Vida' => 'Vida',
            'Propiedad y Accidentes' => 'Propiedad y Accidentes',
            'Empresarial' => 'Empresarial',
            'Personal' => 'Personal',
            'Administrativo' => 'Administrativo',
        ];

        $catIds = [];
        foreach ($categories as $key => $name) {
            $cat = Category::firstOrCreate(['name' => $name]);
            $catIds[$key] = $cat->id;
            $this->line("✓ Category ensured: $name");
        }

        // 3. Helper for Schema v2.0
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
                // Ensure first group has system fields
                $firstGroup = $groups[0];
                
                // Remove any manually added system fields to avoid dupes
                $cleanFields = array_filter($firstGroup['fields'], function($f) {
                    return !in_array($f['name'], ['client_name', 'client_email', 'client_phone']);
                });
                
                $groups[0]['fields'] = array_merge($systemFields, $cleanFields);

                if (in_array($groups[0]['title'], ['Datos del Servicio', 'Información General'])) {
                     $groups[0]['title'] = 'Datos Personales';
                }
            }
            return ['version' => 2, 'groups' => $groups];
        };

        // 4. Define Offerings
        $offerings = [
            [
                'name' => 'Seguros de Salud',
                'category_id' => $catIds['Salud'],
                'type' => 'service',
                'description' => 'Cobertura médica integral. Comisión del 30% mensual.',
                'commission_type' => 'percentage',
                'base_commission' => 30.00,
                'groups' => [
                    [
                        'id' => 'group_extra',
                        'title' => 'Datos del Servicio',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Seguros de Vida',
                'category_id' => $catIds['Vida'],
                'type' => 'service',
                'description' => 'Protección financiera. Comisión fija de $25.',
                'commission_type' => 'fixed',
                'base_commission' => 25.00,
                'groups' => [
                     [
                        'id' => 'group_extra',
                        'title' => 'Datos del Servicio',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Seguros de Carro y Casa',
                'category_id' => $catIds['Propiedad y Accidentes'],
                'type' => 'service',
                'description' => 'Seguros de auto (Personal/Comercial) y hogar. Comisión fija de $25.',
                'commission_type' => 'fixed',
                'base_commission' => 25.00,
                'groups' => [
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
                ]
            ],
            [
                'name' => 'Group Insurance (5 empleados o mas)',
                'category_id' => $catIds['Empresarial'],
                'type' => 'service',
                'description' => 'Seguros colectivos comerciales. Comisión fija de $50.',
                'commission_type' => 'fixed',
                'base_commission' => 50.00,
                'groups' => [
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
                ]
            ],
            [
                'name' => "Business liability o Worker's Comp",
                'category_id' => $catIds['Empresarial'],
                'type' => 'service',
                'description' => 'Seguros empresariales (GL, WC, E&O). Comisión del 10%.',
                'commission_type' => 'percentage',
                'base_commission' => 10.00,
                'groups' => [
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
                ]
            ],
            [
                'name' => 'Taxes personales',
                'category_id' => $catIds['Personal'],
                'type' => 'service',
                'description' => 'Preparación de impuestos personales. Comisión fija de $25.',
                'commission_type' => 'fixed',
                'base_commission' => 25.00,
                'groups' => [
                    [
                        'id' => 'group_extra',
                        'title' => 'Datos del Servicio',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                        ]
                    ]
                ]
            ],
            [
                'name' => 'Taxes corporativos',
                'category_id' => $catIds['Empresarial'],
                'type' => 'service',
                'description' => 'Impuestos para empresas. Comisión fija de $50.',
                'commission_type' => 'fixed',
                'base_commission' => 50.00,
                'groups' => [
                    [
                        'id' => 'group_biz',
                        'title' => 'Datos de la Empresa',
                        'fields' => [
                            ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                            ['name' => 'business_name', 'label' => 'Nombre de la Empresa', 'type' => 'text', 'required' => true],
                        ]
                    ]
                ]
            ],
             [
                'name' => 'Pedir Certificado de Seguro (CDI)',
                'category_id' => $catIds['Administrativo'],
                'type' => 'service',
                'description' => 'Servicio para clientes existentes.',
                'commission_type' => 'fixed',
                'base_commission' => 0.00,
                'is_active' => false,
                'groups' => []
            ]
        ];

        // 5. Update Database
        $bar = $this->output->createProgressBar(count($offerings));
        $bar->start();

        foreach ($offerings as $data) {
            $schema = $withIdentity($data['groups']);
            
            // Clean keys for UpdateOrCreate (remove temporary keys not in DB column list if any, but Offering uses fillable mostly)
            Offering::updateOrCreate(
                ['name' => $data['name']],
                [
                    'owner_employee_id' => $ownerEmployeeId,
                    'category_id' => $data['category_id'],
                    'type' => $data['type'],
                    'description' => $data['description'],
                    'commission_type' => $data['commission_type'] ?? 'fixed',
                    'base_commission' => $data['base_commission'],
                    'is_active' => $data['is_active'] ?? true,
                    'form_schema' => $schema,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Catalog Sync Completed Successfully!');
        
        return 0;
    }
}
