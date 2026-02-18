<?php

namespace Database\Seeders;

use App\Models\Associate;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. PS Admin (Super Admin) - Is an Employee
        // ID: 1, Name: 'PS Administrator', Email: 'psadmin@psrefer.com'
        $psadminProfile = Employee::create([
            'id' => 1, // FORCE ID
            'department' => 'Executive',
            'job_title' => 'System Owner',
            'internal_code' => 'EMP-001',
            'created_at' => '2026-02-10 04:06:07',
            'updated_at' => '2026-02-10 04:06:07',
        ]);

        $psadmin = User::firstOrCreate(
            ['id' => 1],
            [
                'email' => 'psadmin@psrefer.com',
                'name' => 'PS Administrator',
                'password' => '$2y$12$xZj3ulP2abNTSv498NDFWeP2a11IYP7hbeWK2MVf13qZVM6KcGL.6', // Production Hash
                'phone' => '555-0001',
                'is_active' => true,
                'email_verified_at' => '2026-02-10 04:06:08',
                'logo_url' => 'https://ui-avatars.com/api/?name=PS+Administrator&background=random',
                'preferred_currency' => 'USD',
                'profileable_type' => 'App\Models\Employee',
                'profileable_id' => 1,
                'created_at' => '2026-02-10 04:06:08',
                'updated_at' => '2026-02-10 04:06:08',
            ]
        );
        $psadmin->assignRole('psadmin');


        // 2. Admin (Standard Manager) - Is an Employee
        // ID: 2, Name: 'System Manager', Email: 'admin@psrefer.com'
        $adminProfile = Employee::create([
            'id' => 2, // FORCE ID
            'department' => 'Operations',
            'job_title' => 'Manager',
            'internal_code' => 'EMP-002',
            'created_at' => '2026-02-10 04:06:08',
            'updated_at' => '2026-02-10 04:06:08',
        ]);

        $admin = User::firstOrCreate(
            ['id' => 2],
            [
                'email' => 'admin@psrefer.com',
                'name' => 'System Manager',
                'password' => '$2y$12$OF.gtcdXiGWNrsHBD27IW.xIbYWV3v7Gwgt4Yabbo0ZZIgO1qtNzW',
                'phone' => '555-0002',
                'is_active' => true,
                'email_verified_at' => '2026-02-10 04:06:08',
                'logo_url' => 'https://ui-avatars.com/api/?name=System+Manager&background=random',
                'preferred_currency' => 'USD',
                'profileable_type' => 'App\Models\Employee',
                'profileable_id' => 2,
                'created_at' => '2026-02-10 04:06:08',
                'updated_at' => '2026-02-10 04:06:08',
            ]
        );
        $admin->assignRole('admin');


        // 3. Associate (Partner) 1
        // ID: 3, Name: 'Active Partner', Email: 'partner@psrefer.com'
        $associateProfile1 = Associate::create([
            'id' => 1, // Associate ID 1 matches User ID 3 in SQL dump relationships
            'balance' => 0.00,
            'w9_status' => 'verified',
            'category' => 'Realtor',
            'created_at' => '2026-02-10 04:06:08',
            'updated_at' => '2026-02-10 04:06:08',
        ]);

        $associate1 = User::firstOrCreate(
            ['id' => 3],
            [
                'email' => 'partner@psrefer.com',
                'name' => 'Active Partner',
                'password' => '$2y$12$pT5o4sDfoDHMtmv7vtBGNOvBQqxJAn20QJFdi9vwsAUxdLQkmbV3u',
                'phone' => '555-1000',
                'is_active' => true,
                'email_verified_at' => '2026-02-10 04:06:08',
                'logo_url' => 'https://ui-avatars.com/api/?name=Active+Partner&background=random',
                'remember_token' => 'sBVQg226dXcgeGHpIfCIUiesV28oAl1ZM4Z4oM7ShY0iNn17pSaR6np24kaQ',
                'preferred_currency' => 'USD',
                'profileable_type' => 'App\Models\Associate',
                'profileable_id' => 1,
                'created_at' => '2026-02-10 04:06:08',
                'updated_at' => '2026-02-10 04:06:08',
            ]
        );
        $associate1->assignRole('associate');


        // 4. Other Associates (ID 4-8) - Mapped to Associate Models 2-6
        // From dump: User 4 -> Associate 2, User 5 -> Associate 3 ...
        $associatesData = [
            4 => ['email' => 'associate0@example.com', 'name' => 'Associate 0', 'hash' => '$2y$12$L4ToyFQg79GHI/IJWW5TseobgLTU5FLM7jP4560sPL6b5fwQE5eV2', 'aid' => 2],
            5 => ['email' => 'associate1@example.com', 'name' => 'Associate 1', 'hash' => '$2y$12$1/RJW3AVqf59xsvRt3wXVu2zddS6MCoQNnNpaKfczW7ek2kBZfcAO', 'aid' => 3],
            6 => ['email' => 'associate2@example.com', 'name' => 'Associate 2', 'hash' => '$2y$12$IA0d7B/YT843r9N.IRoyWuj9N616wru0oeo3Z/hv.PeT9FavyUPaW', 'aid' => 4],
            7 => ['email' => 'associate3@example.com', 'name' => 'Associate 3', 'hash' => '$2y$12$sHdSAVg7QrkbAU4ESH5UiupSEbO63Pi4EiGPozr7Sq61kwE.WcxZK', 'aid' => 5],
            8 => ['email' => 'associate4@example.com', 'name' => 'Associate 4', 'hash' => '$2y$12$fvktP/FFZnk2Em0OEWUDjOikkkGZz77FIdk/cECt5jvjPaTgsXSnG', 'aid' => 6],
        ];

        foreach ($associatesData as $uid => $data) {
            $prof = Associate::create([
                'id' => $data['aid'],
                'balance' => 0.00,
                'w9_status' => 'pending',
                'created_at' => $uid >= 6 ? '2026-02-10 04:06:09' : '2026-02-10 04:06:08',
                'updated_at' => $uid >= 6 ? '2026-02-10 04:06:09' : '2026-02-10 04:06:08',
            ]);

            $u = User::firstOrCreate(
                ['id' => $uid],
                [
                    'email' => $data['email'],
                    'name' => $data['name'],
                    'password' => $data['hash'],
                    'phone' => '555-100' . ($uid - 4),
                    'is_active' => true,
                    'logo_url' => "https://ui-avatars.com/api/?name=" . urlencode($data['name']) . "&background=random",
                    'preferred_currency' => 'USD',
                    'profileable_type' => 'App\Models\Associate',
                    'profileable_id' => $data['aid'],
                    'created_at' => $uid >= 6 ? '2026-02-10 04:06:09' : '2026-02-10 04:06:08',
                    'updated_at' => $uid >= 6 ? '2026-02-10 04:06:09' : '2026-02-10 04:06:08',
                ]
            );
            $u->assignRole('associate');
        }
    }
}
