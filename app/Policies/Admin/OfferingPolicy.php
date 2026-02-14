<?php

namespace App\Policies\Admin;

use App\Enums\RoleName;
use App\Models\Offering;
use App\Models\User;

class OfferingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleName::adminOrAssociate());
    }

    public function view(User $user, Offering $offering): bool
    {
        return $user->hasRole(RoleName::adminOrAssociate());
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::adminRoles());
    }

    public function update(User $user, Offering $offering): bool
    {
        if ($user->hasRole(RoleName::Admin->value)) {
            return true;
        }

        if ($user->hasRole(RoleName::PsAdmin->value)) {
            $employeeId = $user->employeeProfile()?->id;

            return $employeeId !== null && $offering->owner_employee_id === $employeeId;
        }

        return false;
    }

    public function delete(User $user, Offering $offering): bool
    {
        return $user->hasRole(RoleName::Admin->value);
    }
}
