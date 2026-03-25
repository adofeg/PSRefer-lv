<?php

namespace App\Policies\Admin;

use App\Enums\EmployeeRole;
use App\Enums\AssociateRole;
use App\Models\Referral;
use App\Models\User;

class ReferralPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEmployee() || $user->isAssociate();
    }

    public function view(User $user, Referral $referral): bool
    {
        if ($user->isAdmin() || $user->hasRole(EmployeeRole::PSADMIN->value)) {
            return true;
        }

        return $user->associate?->id === $referral->associate_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->hasRole(AssociateRole::ASSOCIATE->value);
    }

    public function update(User $user, Referral $referral): bool
    {
        if ($user->isAdmin() || $user->hasRole(EmployeeRole::PSADMIN->value)) {
            return true;
        }

        if ($user->hasRole(AssociateRole::ASSOCIATE->value)) {
            return $user->associate?->id === $referral->associate_id;
        }

        return false;
    }

    public function delete(User $user, Referral $referral): bool
    {
        return $user->isAdmin();
    }
}
