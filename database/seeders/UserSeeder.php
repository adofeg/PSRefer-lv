<?php

namespace Database\Seeders;

use App\Models\Associate;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. PS Admin (Super Admin) - Is an Employee
        $psadminProfile = Employee::create([
            'department' => 'Executive',
            'job_title' => 'System Owner',
            'internal_code' => 'EMP-001'
        ]);
        
        $psadmin = $psadminProfile->user()->create([
            'email' => 'psadmin@psrefer.com',
            'name' => 'PS Administrator',
            'password' => Hash::make('password'),
            'phone' => '555-0001',
            'is_active' => true,
            'email_verified_at' => now(),
            'logo_url' => 'https://ui-avatars.com/api/?name=PS+Administrator&background=random',
        ]);
        $psadmin->assignRole('psadmin');

        // 2. Admin (Standard Manager) - Is an Employee
        $adminProfile = Employee::create([
            'department' => 'Operations',
            'job_title' => 'Manager',
            'internal_code' => 'EMP-002'
        ]);

        $admin = $adminProfile->user()->create([
            'email' => 'admin@psrefer.com',
            'name' => 'System Manager',
            'password' => Hash::make('password'),
            'phone' => '555-0002',
            'is_active' => true,
            'email_verified_at' => now(),
            'logo_url' => 'https://ui-avatars.com/api/?name=System+Manager&background=random',
        ]);
        $admin->assignRole('admin');

        // 3. Associate (Partner) 1
        $associateProfile = Associate::create([
            'balance' => 0.00,
            'category' => 'Realtor',
            'w9_status' => 'verified',
        ]);

        $associate = $associateProfile->user()->create([
            'email' => 'partner@psrefer.com',
            'name' => 'Active Partner',
            'password' => Hash::make('password'),
            'phone' => '555-1000',
            'is_active' => true,
            'email_verified_at' => now(),
            'logo_url' => 'https://ui-avatars.com/api/?name=Active+Partner&background=random',
        ]);
        $associate->assignRole('associate');

        // 4. Generate Random Associates with Data
        // Ideally we update UserFactory, but for agility lets just do loop here
        for ($i = 0; $i < 5; $i++) {
             $prof = Associate::create(['balance' => rand(0, 1000)]);
             $u = $prof->user()->create([
                 'name' => "Associate $i",
                 'email' => "associate$i@example.com",
                 'password' => Hash::make('password'),
                 'phone' => '555-1' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                 'is_active' => true,
                 'logo_url' => 'https://ui-avatars.com/api/?name=Associate+' . $i . '&background=random',
             ]);
             $u->assignRole('associate');
        }
    }
}
