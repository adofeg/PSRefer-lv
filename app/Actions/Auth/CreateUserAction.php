<?php

namespace App\Actions\Auth;

use App\Data\Auth\UserData;
use App\Models\User;
use App\Models\UserOfferingLink; // Assuming model exists or will be created
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateUserAction
{
  public function execute(UserData $data): User
  {
    return DB::transaction(function () use ($data) {
      $user = User::create([
        'name' => $data->name,
        'email' => $data->email,
        'password_hash' => Hash::make($data->password), // Using password_hash column as per legacy schema
        'phone' => $data->phone,
        'role' => $data->role,
        'category' => $data->category,
        'referred_by_id' => $data->referred_by_id,
        'is_active' => true,
      ]);

      // Logic: Auto-link to offering if provided (from legacy authRoutes.js)
      if ($data->offering_id) {
        // Determine if we need a model for this or just DB insert.
        // Using DB for now to assume table exists from migration.
        DB::table('user_offering_links')->insert([
          'id' => \Illuminate\Support\Str::uuid(),
          'user_id' => $user->id,
          'offering_id' => $data->offering_id,
          'created_at' => now(),
        ]);
      }

      return $user;
    });
  }
}
