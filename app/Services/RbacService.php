<?php

namespace App\Services;

use App\Enums\PermissionName;
use App\Enums\RoleName;

class RbacService
{
    protected $rolePermissions = [
        RoleName::Admin->value => ['*'], // Logic handled in hasPermission
        RoleName::PsAdmin->value => [
            PermissionName::UserView->value,
            PermissionName::OfferingView->value,
            PermissionName::OfferingCreate->value,
            PermissionName::OfferingEditOwn->value,
            PermissionName::ReferralView->value,
            PermissionName::ReferralUpdateStatus->value,
            PermissionName::CommissionView->value,
            PermissionName::AnalyticsViewAll->value,
        ],
        RoleName::Associate->value => [
            PermissionName::OfferingView->value,
            PermissionName::ReferralViewOwn->value,
            PermissionName::ReferralCreate->value,
            PermissionName::CommissionViewOwn->value,
            PermissionName::AnalyticsViewOwn->value,
        ],
    ];

    public function hasPermission(string $role, string $permission): bool
    {
        if ($role === RoleName::Admin->value) {
            return true;
        }

        $perms = $this->rolePermissions[$role] ?? [];

        return in_array($permission, $perms);
    }

    public function getPermissions(string $role): array
    {
        return $this->rolePermissions[$role] ?? [];
    }
}
