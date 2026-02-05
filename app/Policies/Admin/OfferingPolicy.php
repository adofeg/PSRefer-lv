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
        return $user->hasRole(RoleName::adminRoles());
    }

    public function delete(User $user, Offering $offering): bool
    {
        return $user->hasRole(RoleName::adminRoles());
    }
}
