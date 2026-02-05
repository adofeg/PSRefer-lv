<?php

namespace App\Actions\Dashboard;

use App\Enums\RoleName;
use App\Models\User;

class DetermineDashboardRedirectAction
{
    public function execute(User $user): string
    {
        if ($user->hasRole(RoleName::adminOrAssociate())) {
            return 'admin.dashboard';
        }

        return 'home';
    }
}
