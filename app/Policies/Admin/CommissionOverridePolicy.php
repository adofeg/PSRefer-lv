<?php

namespace App\Policies\Admin;

use App\Enums\RoleName;
use App\Models\CommissionOverride;
use App\Models\User;

class CommissionOverridePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleName::adminRoles());
    }

    public function view(User $user, CommissionOverride $override): bool
    {
        return $user->hasRole(RoleName::adminRoles());
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::adminRoles());
    }

    public function update(User $user, CommissionOverride $override): bool
    {
        return $user->hasRole(RoleName::adminRoles());
    }

    public function delete(User $user, CommissionOverride $override): bool
    {
        return $user->hasRole(RoleName::adminRoles());
    }
}
