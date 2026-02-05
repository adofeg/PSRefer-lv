<?php

namespace App\Actions\Users;

use App\Models\User;

class ToggleUserStatusAction
{
    public function execute(User $user, bool $isActive): User
    {
        $user->update([
            'is_active' => $isActive,
        ]);

        return $user->refresh();
    }
}