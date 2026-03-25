<?php

namespace App\Policies\Admin;

use App\Models\Offering;
use App\Models\User;

class OfferingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEmployee();
    }

    public function view(User $user, Offering $offering): bool
    {
        return $user->isEmployee();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Offering $offering): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Offering $offering): bool
    {
        return $user->isAdmin();
    }
}
