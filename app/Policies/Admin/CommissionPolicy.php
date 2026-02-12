<?php

namespace App\Policies\Admin;

use App\Enums\RoleName;
use App\Models\Commission;
use App\Models\User;

class CommissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin, PSAdmin, and Associate can view commissions (filtered by controller)
        return $user->hasRole([
            RoleName::Admin->value,
            RoleName::PsAdmin->value,
            RoleName::Associate->value
        ]);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Commission $commission): bool
    {
        if ($user->hasRole([RoleName::Admin->value, RoleName::PsAdmin->value])) {
            return true;
        }

        // Associate can view their own commissions
        return $user->associateProfile()?->id === $commission->associate_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Internal requirement: "No puede crear o modificar comisiones directamente" (PSAdmin)
        // Only Admin can create commissions.
        return $user->hasRole(RoleName::Admin->value);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Commission $commission): bool
    {
        // Only Admin can update commissions.
        return $user->hasRole(RoleName::Admin->value);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Commission $commission): bool
    {
        // Only Admin can delete/void commissions.
        return $user->hasRole(RoleName::Admin->value);
    }
}
