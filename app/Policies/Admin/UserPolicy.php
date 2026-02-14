<?php

namespace App\Policies\Admin;

use App\Enums\RoleName;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole([RoleName::Admin->value, RoleName::PsAdmin->value]);
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasRole([RoleName::Admin->value, RoleName::PsAdmin->value]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::Admin->value);
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasRole(RoleName::Admin->value);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasRole(RoleName::Admin->value);
    }
}
