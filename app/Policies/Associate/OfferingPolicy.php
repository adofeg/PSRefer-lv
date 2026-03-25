<?php

namespace App\Policies\Associate;

use App\Models\Offering;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfferingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAssociate();
    }

    public function view(User $user, Offering $offering): bool
    {
        if (! $user->isAssociate()) {
            return false;
        }

        // Associates can only view offerings that don't conflict with their own category
        $associate = $user->associate;
        if (! $associate) {
            return false;
        }

        return $offering->is_active && $offering->category_id !== $associate->category_id;
    }
}
