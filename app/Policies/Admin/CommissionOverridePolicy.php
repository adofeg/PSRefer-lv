<?php

namespace App\Policies\Admin;

use App\Enums\EmployeeRole;
use App\Models\CommissionOverride;
use App\Models\User;

class CommissionOverridePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(EmployeeRole::ADMIN->values());
    }

    public function view(User $user, CommissionOverride $override): bool
    {
        return $user->hasRole(EmployeeRole::ADMIN->values());
    }

    public function create(User $user): bool
    {
        return $user->hasRole(EmployeeRole::ADMIN->values());
    }

    public function update(User $user, CommissionOverride $override): bool
    {
        return $user->hasRole(EmployeeRole::ADMIN->values());
    }

    public function delete(User $user, CommissionOverride $override): bool
    {
        return $user->hasRole(EmployeeRole::ADMIN->values());
    }
}
