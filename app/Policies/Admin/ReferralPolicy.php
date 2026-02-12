<?php

namespace App\Policies\Admin;

use App\Enums\RoleName;
use App\Models\Referral;
use App\Models\User;

class ReferralPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleName::adminOrAssociate());
    }

    public function view(User $user, Referral $referral): bool
    {
        if ($user->hasRole(RoleName::adminRoles())) {
            return true;
        }

        return $user->associateProfile()?->id === $referral->associate_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole([RoleName::Admin->value, RoleName::Associate->value]);
    }

    public function update(User $user, Referral $referral): bool
    {
        if ($user->hasRole(RoleName::adminRoles())) {
            return true;
        }

        if ($user->hasRole(RoleName::Associate->value)) {
            return $user->associateProfile()?->id === $referral->associate_id;
        }

        return false;
    }

    public function delete(User $user, Referral $referral): bool
    {
        return $user->hasRole(RoleName::Admin->value);
    }
}
