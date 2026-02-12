<?php

namespace App\Actions\Dashboard;

use App\Enums\RoleName;
use App\Models\User;

class DetermineDashboardRedirectAction
{
    public function execute(User $user): string
    {
        if ($user->hasRole(RoleName::adminOrPsAdmin())) {
            return 'admin.dashboard';
        }

        if ($user->hasRole(RoleName::associate())) {
            return 'associate.dashboard';
        }

        return 'home';
    }
}
