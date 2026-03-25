<?php

namespace App\Policies\Admin;

use App\Enums\EmployeeRole;
use App\Models\Offering;
use App\Models\User;

class OfferingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEmployee() || $user->isAssociate();
    }

    public function view(User $user, Offering $offering): bool
    {
        return $user->isEmployee() || $user->isAssociate();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasRole(EmployeeRole::PSADMIN->value);
    }

    public function update(User $user, Offering $offering): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->hasRole(EmployeeRole::PSADMIN->value)) {
            $employeeId = $user->employee?->id;

            return $employeeId !== null && $offering->owner_employee_id === $employeeId;
        }

        return false;
    }

    public function delete(User $user, Offering $offering): bool
    {
        return $user->isAdmin();
    }
}
