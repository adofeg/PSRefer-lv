<?php

namespace App\Actions\Auth;

use App\Data\Auth\UserData;
use App\Enums\RoleName;
use App\Models\User;
use App\Models\Associate;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
          'logo_url' => 'https://ui-avatars.com/api/?name='.urlencode($data->name).'&background=random',
        ]);

        $user->assignRole(RoleName::Associate->value);

        if ($associate->referrer_id) {
          $associate->addToNetwork($associate->referrer_id);
        }
      } else {
        $employee = Employee::create([
          'department' => 'Administration',
          'job_title' => 'Staff',
          'internal_code' => null,
        ]);

        $user = $employee->user()->create([
          'name' => $data->name,
          'email' => $data->email,
          'password' => Hash::make($data->password),
          'phone' => $data->phone,
          'is_active' => true,
          'logo_url' => 'https://ui-avatars.com/api/?name='.urlencode($data->name).'&background=random',
        ]);

        $user->assignRole($data->role);
      }

      // Logic: Auto-link to offering if provided (from legacy authRoutes.js)
      if ($data->offering_id) {
        // Using DB for now to assume table exists from migration.
        DB::table('associate_offering_links')->insert([
          'associate_id' => $user->associateProfile()?->id,
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
