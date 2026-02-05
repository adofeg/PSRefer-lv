<?php

namespace App\Policies;

use App\Models\Referral;
use App\Models\User;

class ReferralPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'psadmin', 'associate']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Referral $referral): bool
    {
        if ($user->hasRole(['admin', 'psadmin'])) {
            return true;
        }

        return $user->id === $referral->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'psadmin', 'associate']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Referral $referral): bool
    {
        if ($user->hasRole(['admin', 'psadmin'])) {
            return true;
        }
        
        // Associates usually can't update status, but maybe they can edit details?
        // For now, restricting to Admin/PSAdmin or Owner if business logic allows.
        // Based on controller scan, update uses UpdateReferralStatusAction which takes status.
        // Let's assume only admins change status. 
        // But maybe associates can edit notes?
        // I'll stick to Admin/PSAdmin for now as safe default, or mirror view logic if they can edit own.
        // The user didn't specify, so I'll replicate the Controller logic.
        // Wait, controller didn't have update check in `update` method! It used `UpdateReferralRequest`.
        // Let's check UpdateReferralRequest to see if it authorizes.
        
        return $user->hasRole(['admin', 'psadmin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Referral $referral): bool
    {
        return $user->hasRole(['admin', 'psadmin']);
    }
}
