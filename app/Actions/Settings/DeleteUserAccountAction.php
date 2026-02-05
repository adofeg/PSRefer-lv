<?php

namespace App\Actions\Settings;

use App\Models\User;
use Illuminate\Session\Store;

class DeleteUserAccountAction
{
    public function execute(User $user, Store $session): void
    {
        $user->delete();

        $session->invalidate();
        $session->regenerateToken();
    }
}
