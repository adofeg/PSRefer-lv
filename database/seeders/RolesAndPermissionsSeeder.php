<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\AssociateRole;
use App\Enums\EmployeeRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // --- Layer Permissions ---
        $permissionEmployee = Permission::firstOrCreate(['name' => 'employee']);
        $permissionAssociate = Permission::firstOrCreate(['name' => 'associate']);

        // --- Role Creation & Layer Mapping ---

        // Employee Roles (Admin / PsAdmin)
        foreach (EmployeeRole::cases() as $roleEnum) {
            $role = Role::firstOrCreate(['name' => $roleEnum->value]);
            $role->syncPermissions([$permissionEmployee]);
        }

        // Associate Roles
        foreach (AssociateRole::cases() as $roleEnum) {
            $role = Role::firstOrCreate(['name' => $roleEnum->value]);
            $role->syncPermissions([$permissionAssociate]);
        }
    }
}
