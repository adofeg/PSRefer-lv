<?php

namespace App\Actions\Auth;

use App\Data\Auth\UserData;
use App\Enums\RoleName;
use App\Models\Associate;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateUserAction
{
    public function __construct(
        protected \App\Services\AuditService $auditService
    ) {}

    public function execute(UserData $data): User
    {
        return DB::transaction(function () use ($data) {
            if ($data->role === RoleName::Associate->value) {
                $associate = Associate::create([
                    'category' => $data->category,
                    'referrer_id' => $data->referrer_id,
                ]);

                $user = $associate->user()->create([
                    'name' => $data->name,
                    'email' => $data->email,
                    'password' => Hash::make($data->password),
                    'phone' => $data->phone,
                    'is_active' => true,
                ]);

                $user->assignRole(RoleName::Associate->value);

            } else {
                $employee = Employee::create([]);

                $user = $employee->user()->create([
                    'name' => $data->name,
                    'email' => $data->email,
                    'password' => Hash::make($data->password),
                    'phone' => $data->phone,
                    'is_active' => true,
                ]);

                $user->assignRole($data->role);
            }

            // Logic: Auto-link to offering if provided (from legacy authRoutes.js)
            if ($data->offering_id) {
                // Using DB for now to assume table exists from migration.
                DB::table('associate_offering_links')->insert([
                    'associate_id' => $user->associate?->id,
                    'offering_id' => $data->offering_id,
                    'created_at' => now(),
                ]);
            }

            $this->auditService->logAction(
                $user,
                'CREATE',
                "User '{$user->name}' created with role '{$data->role}'",
                null,
                ['email' => $user->email, 'role' => $data->role]
            );

            return $user;
        });
    }
}
