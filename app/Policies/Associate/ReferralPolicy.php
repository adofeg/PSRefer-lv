<?php

namespace App\Policies\Associate;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReferralPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAssociate();
    }

    public function view(User $user, Referral $referral): bool
    {
        return $user->isAssociate() && $user->associate?->id === $referral->associate_id;
    }

    public function create(User $user): bool
    {
        return $user->isAssociate();
    }

    public function update(User $user, Referral $referral): bool
    {
        return $user->isAssociate() && $user->associate?->id === $referral->associate_id;
    }

    public function delete(User $user, Referral $referral): bool
    {
        return false; // Associates cannot delete referrals
    }
}
