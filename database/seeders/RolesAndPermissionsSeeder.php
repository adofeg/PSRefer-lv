<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Roles
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $psadmin = Role::firstOrCreate(['name' => 'psadmin', 'guard_name' => 'web']);
        $associate = Role::firstOrCreate(['name' => 'associate', 'guard_name' => 'web']);

        // Assignment is handled in UserSeeder / registration flow.
    }
}
