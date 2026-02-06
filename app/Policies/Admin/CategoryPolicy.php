<?php

namespace App\Policies\Admin;

use App\Enums\RoleName;
use App\Models\Category;
use App\Models\User;

class CategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(RoleName::Admin->value);
    }

    public function view(User $user, Category $category): bool
    {
        return $user->hasRole(RoleName::Admin->value);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(RoleName::Admin->value);
    }

    public function update(User $user, Category $category): bool
    {
        return $user->hasRole(RoleName::Admin->value);
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasRole(RoleName::Admin->value);
    }
}