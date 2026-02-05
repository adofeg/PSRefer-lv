<?php

namespace App\Policies;

use App\Models\Offering;
use App\Models\User;

class OfferingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Associates need to see offerings to select them for referrals
        return $user->hasRole(['admin', 'psadmin', 'associate']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Offering $offering): bool
    {
        return $user->hasRole(['admin', 'psadmin', 'associate']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'psadmin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Offering $offering): bool
    {
        return $user->hasRole(['admin', 'psadmin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Offering $offering): bool
    {
        return $user->hasRole(['admin', 'psadmin']);
    }
}
