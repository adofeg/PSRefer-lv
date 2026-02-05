<?php
// Script rápido para inicializar la base de datos
require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;

try {
    echo "Iniciando configuración de la base de datos...\n";
    
    // Intentar crear la base de datos si no existe (esto puede fallar si ya existe, lo cual está bien)
    echo "Ejecutando migraciones...\n";
    Artisan::call('migrate', ['--force' => true]);
    echo Artisan::output();
    
    echo "Creando usuario de prueba...\n";
    // Crear un usuario administrador de ejemplo
    $userClass = '\App\Models\User';
    if(class_exists($userClass)) {
        $user = $userClass::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'role' => 'psadmin',
                'is_active' => true
            ]
        );
        echo "Usuario admin creado: admin@example.com\n";
    }
    
    echo "¡Despliegue completado exitosamente!\n";
    echo "La aplicación está lista para usarse.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}