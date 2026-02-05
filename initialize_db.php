<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Offering;
use App\Models\Referral;
use App\Models\Employee;
use App\Models\Associate;

// Iniciar la aplicación Laravel
require_once __DIR__.'/vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Crear usuario administrador
echo "Creando usuario administrador...\n";

$employee = Employee::create([
    'department' => 'Executive',
    'job_title' => 'System Owner',
    'internal_code' => 'EMP-INIT',
]);

$user = $employee->user()->create([
    'name' => 'Admin User',
    'email' => 'admin@psrefer.local',
    'password' => Hash::make('password'),
    'is_active' => true,
    'phone' => '555-0001',
    'logo_url' => 'https://ui-avatars.com/api/?name=Admin+User&background=random',
]);

$user->assignRole('psadmin');

echo "Usuario admin creado: admin@psrefer.local / password\n";

// Crear un usuario asociado
echo "Creando usuario asociado...\n";

$associateProfile = Associate::create([
    'balance' => 0,
]);

$associate = $associateProfile->user()->create([
    'name' => 'Associate User',
    'email' => 'associate@psrefer.local',
    'password' => Hash::make('password'),
    'is_active' => true,
    'phone' => '555-0100',
    'logo_url' => 'https://ui-avatars.com/api/?name=Associate+User&background=random',
]);

$associate->assignRole('associate');

echo "Usuario asociado creado: associate@psrefer.local / password\n";

// Crear una oferta de ejemplo
echo "Creando oferta de ejemplo...\n";

$offering = Offering::create([
    'name' => 'Servicio de Consultoría',
    'description' => 'Oferta de servicios profesionales de consultoría',
    'base_price' => 1000.00,
    'commission_rate' => 10.00,
    'is_active' => true,
    'owner_employee_id' => $employee->id,
]);

echo "Oferta creada: {$offering->name}\n";

// Crear una referencia de ejemplo
echo "Creando referencia de ejemplo...\n";

$referral = Referral::create([
    'associate_id' => $associateProfile->id,
    'offering_id' => $offering->id,
    'client_name' => 'Cliente de Prueba',
    'client_contact' => 'cliente@ejemplo.com',
    'status' => 'Prospecto',
]);

echo "Referencia creada: {$referral->client_name}\n";

echo "\n¡Despliegue completado exitosamente!\n";
echo "Usuarios de prueba creados:\n";
echo "- Admin: admin@psrefer.local / password\n";
echo "- Associate: associate@psrefer.local / password\n";
echo "\nLa aplicación PS Refer está lista para usarse.\n";