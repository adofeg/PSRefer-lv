<?php

namespace App\Actions\Dashboard;

// Enum no longer needed here if using helpers
use App\Models\User;

class DetermineDashboardRedirectAction
{
    public function execute(User $user): string
    {
        if ($user->isEmployee()) {
            return 'admin.dashboard';
        }

        if ($user->isAssociate()) {
            return 'associate.dashboard';
        }

        return 'home';
    }
}
