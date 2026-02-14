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
        $ownerEmployeeId = $psadmin?->employeeProfile()?->id;
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

        // 1. Seguros de Salud (30% Mensual)
        Offering::updateOrCreate(
            ['name' => 'Seguros de Salud'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $health->id,
                'type' => 'service',
                'description' => 'Cobertura médica integral. Comisión del 30% mensual.',
                'commission_rate' => 30.00,
                'is_active' => false, // DEACTIVATED - Not in PDF list
                'form_schema' => [
                    ['name' => 'client_name', 'label' => 'Nombre y Apellido', 'type' => 'text', 'required' => true],
                    ['name' => 'client_phone', 'label' => 'Teléfono', 'type' => 'tel', 'required' => true],
                    ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                    ['name' => 'dob', 'label' => 'Fecha de Nacimiento', 'type' => 'date', 'required' => false],
                    ['name' => 'family_members', 'label' => 'Miembros de la familia a asegurar', 'type' => 'textarea', 'required' => false],
                ],
                'commission_rules' => [
                    ['condition' => 'default', 'commission_rate' => 30, 'label' => 'Comisión Mensual', 'roles' => ['associate']],
                ],
            ]
        );

        // 2. Seguros de Vida (Flat $25)
        Offering::updateOrCreate(
            ['name' => 'Seguros de Vida'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $life->id,
                'type' => 'service',
                'description' => 'Protección financiera. Comisión fija de $25.',
                'commission_rate' => 0,
                'base_commission' => 25.00, // FIXED: Display correct flat fee
                'is_active' => false, // DEACTIVATED - Not in PDF list
                'form_schema' => [
                    ['name' => 'client_name', 'label' => 'Nombre y Apellido', 'type' => 'text', 'required' => true],
                    ['name' => 'client_phone', 'label' => 'Teléfono', 'type' => 'tel', 'required' => true],
                    ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                    ['name' => 'dob', 'label' => 'Fecha de Nacimiento', 'type' => 'date', 'required' => false],
                    ['name' => 'beneficiary', 'label' => 'Beneficiario Principal', 'type' => 'text', 'required' => false],
                    ['name' => 'coverage_amount', 'label' => 'Monto de Cobertura Deseado', 'type' => 'text', 'required' => false],
                ],
                'commission_rules' => [
                    ['condition' => 'default', 'fixed' => 25, 'label' => 'Tarifa Fija', 'roles' => ['associate']],
                ],
            ]
        );

        // 3. Taxes Personales (Flat $25)
        Offering::updateOrCreate(
            ['name' => 'Taxes Personales'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $personal->id,
                'type' => 'service',
                'description' => 'Preparación de impuestos personales. Comisión fija de $25.',
                'commission_rate' => 0,
                'base_commission' => 25.00, // FIXED: Display correct flat fee
                'is_active' => false, // DEACTIVATED - Not in PDF list
                'form_schema' => [
                    ['name' => 'client_name', 'label' => 'Nombre y Apellido', 'type' => 'text', 'required' => true],
                    ['name' => 'client_phone', 'label' => 'Teléfono', 'type' => 'tel', 'required' => true],
                    ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                    ['name' => 'tax_year', 'label' => 'Año Fiscal', 'type' => 'number', 'required' => true],
                    ['name' => 'filing_status', 'label' => 'Estado Civil (Filing Status)', 'type' => 'select', 'options' => ['Single', 'Married Filing Jointly', 'Married Filing Separately', 'Head of Household'], 'required' => true],
                ],
                'commission_rules' => [
                    ['condition' => 'default', 'fixed' => 25, 'label' => 'Tarifa Fija', 'roles' => ['associate']],
                ],
            ]
        );

        // 4. Taxes Corporativos (Flat $50)
        Offering::updateOrCreate(
            ['name' => 'Taxes Corporativos'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Servicios fiscales para corporaciones. Comisión fija de $50.',
                'commission_rate' => 0,
                'base_commission' => 50.00, // FIXED: Display correct flat fee
                'is_active' => false, // DEACTIVATED - Not in PDF list
                'form_schema' => [
                    ['name' => 'company_name', 'label' => 'Nombre de Empresa', 'type' => 'text', 'required' => true],
                    ['name' => 'ein', 'label' => 'EIN', 'type' => 'text', 'required' => true],
                    ['name' => 'client_phone', 'label' => 'Teléfono', 'type' => 'tel', 'required' => true],
                    ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                    ['name' => 'fiscal_year_end', 'label' => 'Cierre de Año Fiscal', 'type' => 'date', 'required' => false],
                ],
                'commission_rules' => [
                    ['condition' => 'default', 'fixed' => 50, 'label' => 'Tarifa Fija', 'roles' => ['associate']],
                ],
            ]
        );

        // 5. Group Insurance (Flat $50)
        Offering::updateOrCreate(
            ['name' => 'Group Insurance'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Seguros colectivos (5+ empleados). Comisión fija de $50.',
                'commission_rate' => 0,
                'base_commission' => 50.00, // FIXED: Display correct flat fee
                'is_active' => false, // DEACTIVATED - Not in PDF list
                'form_schema' => [
                    ['name' => 'company_name', 'label' => 'Nombre de Empresa', 'type' => 'text', 'required' => true],
                    ['name' => 'contact_person', 'label' => 'Persona de Contacto', 'type' => 'text', 'required' => true],
                    ['name' => 'client_phone', 'label' => 'Teléfono', 'type' => 'tel', 'required' => true],
                    ['name' => 'client_state', 'label' => 'Estado', 'type' => 'text', 'required' => true],
                    ['name' => 'employee_count', 'label' => 'Número de Empleados', 'type' => 'number', 'required' => true, 'min' => 5],
                    ['name' => 'census_file', 'label' => 'Censo de Empleados (Opcional)', 'type' => 'file', 'required' => false],
                ],
                'commission_rules' => [
                    ['condition' => 'default', 'fixed' => 50, 'label' => 'Tarifa Fija', 'roles' => ['associate']],
                ],
            ]
        );

        // 6. Nueva Cotización: Seguro Comercial (10%)
        // Matches "BUSINESS INSURANCE QUOTE SHEET"
        Offering::updateOrCreate(
            ['name' => 'Nueva Cotización: Seguro Comercial'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Solicita una cotización para General Liability y cancelación Workers Comp. Comisión del 10%.',
                'commission_rate' => 10.00,
                'is_active' => true,
                'commission_config' => ['percentage' => 10], // BACKEND MATH
                'form_schema' => [
                    // INFORMACIÓN DEL SOLICITANTE
                    ['name' => 'company_name', 'label' => 'Nombre de la Empresa', 'type' => 'text', 'required' => true],
                    ['name' => 'ein', 'label' => 'E.I.N.', 'type' => 'text', 'required' => true],
                    ['name' => 'entity_type', 'label' => 'Tipo de Entidad', 'type' => 'select', 'options' => ['Individual', 'L.L.C', 'Otra'], 'required' => true],
                    ['name' => 'address', 'label' => 'Dirección Completa (Calle, Ciudad, Estado, Zip)', 'type' => 'text', 'required' => true],
                    ['name' => 'owners_count', 'label' => 'Cuantos Dueños', 'type' => 'number', 'required' => true],
                    ['name' => 'client_phone', 'label' => 'Teléfono', 'type' => 'tel', 'required' => true],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['name' => 'owner_names', 'label' => 'Nombre de los Dueño(s)', 'type' => 'textarea', 'required' => true],
                    ['name' => 'owner_dobs', 'label' => 'Fecha de Nacimiento de Dueño(s)', 'type' => 'text', 'required' => true, 'placeholder' => 'Ej: 01/01/1980, 05/12/1985'],
                    ['name' => 'business_activity', 'label' => 'Actividad de la Empresa', 'type' => 'textarea', 'required' => true],
                    ['name' => 'opening_date', 'label' => 'Fecha de Apertura de su Empresa', 'type' => 'date', 'required' => true],

                    // PRODUCTO
                    ['name' => 'products_interested', 'label' => 'Producto', 'type' => 'checkbox', 'checkboxLabel' => 'General Liability / Workers Comp'],
                    ['name' => 'gl_coverage', 'label' => 'General Liability Cobertura $', 'type' => 'number', 'required' => false],
                    ['name' => 'wc_coverage', 'label' => 'Workers Compensation Cobertura $', 'type' => 'number', 'required' => false],
                    ['name' => 'employees_count', 'label' => 'Cuantos Empleados Tiene', 'type' => 'number', 'required' => true],
                    ['name' => 'annual_payroll', 'label' => 'Nomina Anual (Payroll) $$', 'type' => 'number', 'required' => true],
                    ['name' => 'contractor_payroll', 'label' => 'Nomina Anual a Contratista $$', 'type' => 'number', 'required' => false],
                    ['name' => 'annual_sales', 'label' => 'Ventas Anuales $$', 'type' => 'number', 'required' => true],

                    // INFORMACION TARJETA (Opcional)
                    ['name' => 'card_name', 'label' => 'Nombre en Tarjeta (Opcional)', 'type' => 'text', 'required' => false],
                    ['name' => 'card_number', 'label' => 'Numero de Tarjeta (Opcional)', 'type' => 'text', 'required' => false],
                    ['name' => 'card_exp', 'label' => 'Exp (Opcional)', 'type' => 'text', 'required' => false],
                    ['name' => 'card_cvc', 'label' => 'CVC (Opcional)', 'type' => 'text', 'required' => false],
                ],
                'commission_rules' => [
                    ['condition' => 'default', 'commission_rate' => 10, 'label' => 'Comisión Estándar', 'roles' => ['associate']],
                ],
            ]
        );

        // 7. E&O - Personal (10%)
        // Matches "E&O APPLICATION INDIVIDUAL"
        Offering::updateOrCreate(
            ['name' => 'Errores y Omisiones (Personal)'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Seguro E&O para individuos (Personal).',
                'commission_rate' => 10.00,
                'is_active' => true,
                'commission_config' => ['percentage' => 10], // BACKEND MATH
                'form_schema' => [
                    ['name' => 'rep_name', 'label' => 'Nombre del Representante', 'type' => 'text', 'required' => true],
                    ['name' => 'client_phone', 'label' => 'Teléfono', 'type' => 'tel', 'required' => true],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['name' => 'dob', 'label' => 'Fecha de Nacimiento', 'type' => 'date', 'required' => true],
                    ['name' => 'address', 'label' => 'Dirección', 'type' => 'text', 'required' => true],
                    ['name' => 'occupation', 'label' => 'Ocupación', 'type' => 'text', 'required' => true],
                    ['name' => 'years_operating', 'label' => 'Cuantos años operando su negocio', 'type' => 'number', 'required' => true],
                    ['name' => 'has_branches', 'label' => 'Tiene otras sucursales (Si/No)', 'type' => 'select', 'options' => ['Si', 'No'], 'required' => true],
                    ['name' => 'partners_count', 'label' => 'Cuantos socios en su negocio', 'type' => 'number', 'required' => true],
                    ['name' => 'employees_count', 'label' => 'Cuantos empleados tiene', 'type' => 'number', 'required' => true],
                    ['name' => 'subcontractors_pay', 'label' => 'Pago a sub-contractors prox 12 meses', 'type' => 'number', 'required' => false],
                    ['name' => 'has_eo', 'label' => '¿Tiene ahora un E&O?', 'type' => 'select', 'options' => ['Si', 'No'], 'required' => true],
                    ['name' => 'projected_sales', 'label' => 'Ventas próximos 12 meses', 'type' => 'number', 'required' => true],
                    // "Effective date", "Payment", "Card number" are usually handled in a later stage or via general notes, but kept simple here.
                ],
                'commission_rules' => [
                    ['condition' => 'default', 'commission_rate' => 10, 'label' => 'Comisión Estándar', 'roles' => ['associate']],
                ],
            ]
        );

        // 8. E&O - Empresa (10%)
        // Matches "E&O APPLICATION DE EMPRESA"
        Offering::updateOrCreate(
            ['name' => 'Errores y Omisiones (Empresa)'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $business->id,
                'type' => 'service',
                'description' => 'Seguro E&O para empresas. Comisión del 10%.',
                'commission_rate' => 10.00,
                'is_active' => true,
                'commission_config' => ['percentage' => 10], // BACKEND MATH
                'form_schema' => [
                    ['name' => 'company_name', 'label' => 'Nombre de la Empresa', 'type' => 'text', 'required' => true],
                    ['name' => 'entity_type', 'label' => 'Tipo de Entidad', 'type' => 'select', 'options' => ['LLC', 'CORP', 'OTRO'], 'required' => true],
                    ['name' => 'rep_name', 'label' => 'Nombre del Representante', 'type' => 'text', 'required' => true],
                    ['name' => 'client_phone', 'label' => 'Teléfono', 'type' => 'tel', 'required' => true],
                    ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
                    ['name' => 'dob', 'label' => 'Fecha de Nacimiento (Representante)', 'type' => 'date', 'required' => false],
                    ['name' => 'address', 'label' => 'Dirección', 'type' => 'text', 'required' => true],
                    ['name' => 'occupation', 'label' => 'Ocupación', 'type' => 'text', 'required' => true],
                    ['name' => 'year_opened', 'label' => 'En qué año abrió su empresa', 'type' => 'number', 'required' => true],
                    ['name' => 'has_branches', 'label' => 'Tiene otras sucursales (Si/No)', 'type' => 'select', 'options' => ['Si', 'No'], 'required' => true],
                    ['name' => 'partners_count', 'label' => 'Cuantos socios en su negocio', 'type' => 'number', 'required' => true],
                    ['name' => 'employees_count', 'label' => 'Cuantos empleados tiene', 'type' => 'number', 'required' => true],
                    ['name' => 'subcontractors_pay', 'label' => 'Pago a sub-contractors prox 12 meses', 'type' => 'number', 'required' => false],
                    ['name' => 'has_eo', 'label' => '¿Tiene ahora un E&O?', 'type' => 'select', 'options' => ['Si', 'No'], 'required' => true],
                    ['name' => 'projected_sales', 'label' => 'Ventas próximos 12 meses', 'type' => 'number', 'required' => true],
                    ['name' => 'ein_file', 'label' => 'Adjuntar copia del EIN number o registro de SunBiz', 'type' => 'file', 'required' => true],
                ],
                'commission_rules' => [
                    ['condition' => 'default', 'commission_rate' => 10, 'label' => 'Comisión Estándar', 'roles' => ['associate']],
                ],
            ]
        );

        // 9. Seguro de Auto - Personal (Flat $25)
        // Matches "CUESTIONARIO POLIZA DE AUTO"
        Offering::updateOrCreate(
            ['name' => 'Seguro de Auto (Personal)'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $property->id,
                'type' => 'service',
                'description' => 'Poliza de auto personal. Comisión fija de $25.',
                'commission_rate' => 0,
                'base_commission' => 25.00, // FIXED: Display correct flat fee
                'is_active' => true,
                'commission_config' => ['fixed_amount' => 25], // BACKEND MATH
                'form_schema' => [
                    ['name' => 'insured_names_address', 'label' => 'Nombre todos los asegurados y dirección actual', 'type' => 'textarea', 'required' => true],
                    ['name' => 'household_drivers', 'label' => 'Nombre de todos los mayores de 15 que viven en la propiedad', 'type' => 'textarea', 'required' => true],
                    ['name' => 'vins', 'label' => 'Numero VIN de todos los autos que van a asegurar', 'type' => 'textarea', 'required' => true],
                    ['name' => 'prior_claims', 'label' => '¿Tuvo algún reclamo al PIP o accidente en últimos 36 meses?', 'type' => 'select', 'options' => ['Si', 'No'], 'required' => true],
                    ['name' => 'coverage_needs', 'label' => 'Tipo de coberturas (Comp/Collision si financiado)', 'type' => 'textarea', 'required' => true],
                    ['name' => 'principal_contact_info', 'label' => 'PRINCIPAL DE LA POLIZA: Nombre, Telefono, Email', 'type' => 'textarea', 'required' => true],

                    // Documents
                    ['name' => 'declaration_page', 'label' => 'Declaration Page (Seguro actual)', 'type' => 'file', 'required' => false],
                    ['name' => 'licenses', 'label' => 'Licencias de conducir de todos los conductores', 'type' => 'file', 'required' => true],
                    ['name' => 'car_photos', 'label' => 'Fotos de todos los autos (Millas y Lado del vehiculo)', 'type' => 'file', 'required' => true],
                ],
                'commission_rules' => [
                    ['condition' => 'default', 'fixed' => 25, 'label' => 'Tarifa Fija', 'roles' => ['associate']],
                ],
            ]
        );

        // 10. Seguro de Auto - Comercial (Flat $25)
        // Matches "COMMERCIAL AUTO INSURANCE APPLICATION"
        Offering::updateOrCreate(
            ['name' => 'Seguro de Auto (Comercial)'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $property->id,
                'type' => 'service',
                'description' => 'Poliza de auto comercial/Trucking. Comisión fija de $25.',
                'commission_rate' => 0,
                'base_commission' => 25.00, // FIXED: Display correct flat fee
                'is_active' => true,
                'commission_config' => ['fixed_amount' => 25], // BACKEND MATH
                'form_schema' => [
                    ['name' => 'company_name', 'label' => 'Información de la empresa o Nombre Comercial', 'type' => 'text', 'required' => true],
                    ['name' => 'dot_number', 'label' => 'Número de DOT (Si corresponde)', 'type' => 'text', 'required' => false],
                    ['name' => 'cargo_type', 'label' => 'Tipo de Carga a Transportar', 'type' => 'text', 'required' => true],
                    ['name' => 'interstate', 'label' => '¿Se transporta fuera del estado?', 'type' => 'select', 'options' => ['Si', 'No', 'No Aplica'], 'required' => true],

                    // Documents
                    ['name' => 'declaration_page', 'label' => 'Declaration Page Vigente (Incluyendo seguro personal)', 'type' => 'file', 'required' => true],
                    ['name' => 'licenses', 'label' => 'Licencias de conducir de todos los conductores', 'type' => 'file', 'required' => true],
                    ['name' => 'registration', 'label' => 'Matrícula/Registracion del Vehículo', 'type' => 'file', 'required' => true],
                    ['name' => 'coverage_file', 'label' => 'Coberturas Necesarias (Documento opcional)', 'type' => 'file', 'required' => false],
                    ['name' => 'coverage_text', 'label' => 'Coberturas Necesarias (Texto)', 'type' => 'textarea', 'required' => false],
                ],
                'commission_rules' => [
                    ['condition' => 'default', 'fixed' => 25, 'label' => 'Tarifa Fija', 'roles' => ['associate']],
                ],
            ]
        );

        // 11. Solicitud de Certificado (CDI) - No Commission
        Offering::updateOrCreate(
            ['name' => 'Servicio: Pedir Certificado de Seguro'],
            [
                'owner_employee_id' => $ownerEmployeeId,
                'category_id' => $administrative->id,
                'type' => 'service',
                'description' => 'Servicio administrativo para clientes EXISTENTES que necesitan un certificado (Liability, Workers Comp).',
                'commission_rate' => null,
                'base_commission' => 0.00,
                'is_active' => true,
                'commission_config' => ['fixed_amount' => 0], // BACKEND MATH
                'form_schema' => [
                    ['name' => 'holder', 'label' => 'Titular', 'type' => 'text', 'required' => true],
                    ['name' => 'insured', 'label' => 'Asegurado', 'type' => 'text', 'required' => true],
                    ['name' => 'cert_type', 'label' => 'Tipo de certificado', 'type' => 'select', 'required' => true, 'options' => 'Liability, Workers Comp, Otros'],
                    ['name' => 'date_required', 'label' => 'Fecha requerida', 'type' => 'date', 'required' => true],
                    ['name' => 'shipping_address', 'label' => 'Dirección de envío', 'type' => 'text', 'required' => true],
                ],
            ]
        );
    }
}
