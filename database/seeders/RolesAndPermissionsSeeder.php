<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

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

        // Migrate Old Roles to Spatie Roles
        $users = User::all();

        foreach ($users as $user) {
            $legacyRole = $user->role; // Accessing the string column

            switch ($legacyRole) {
                case 'admin':
                    $user->assignRole($admin);
                    break;
                case 'psadmin':
                    $user->assignRole($psadmin);
                    break;
                case 'associate':
                default:
                    $user->assignRole($associate);
                    break;
            }
        }
    }
}
