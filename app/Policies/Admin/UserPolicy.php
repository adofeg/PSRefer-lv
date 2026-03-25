<?php

namespace App\Policies\Admin;

use App\Enums\EmployeeRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole([EmployeeRole::ADMIN->value, EmployeeRole::PSADMIN->value]);
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasRole([EmployeeRole::ADMIN->value, EmployeeRole::PSADMIN->value]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(EmployeeRole::ADMIN->value);
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasRole(EmployeeRole::ADMIN->value);
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasRole(EmployeeRole::ADMIN->value);
    }
}
