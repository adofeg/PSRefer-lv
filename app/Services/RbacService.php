<?php

namespace App\Services;

class RbacService
{
  // Permissions
  const PERM_USER_VIEW = 'user:view';
  const PERM_REFERRAL_UPDATE_STATUS = 'referral:update_status';
  const PERM_OFFERING_CREATE = 'offering:create';

  // Add other constants as needed

  protected $rolePermissions = [
    'admin' => ['*'], // Logic handled in hasPermission
    'psadmin' => [
      'user:view',
      'offering:view',
      'offering:create',
      'offering:edit_own',
      'referral:view',
      'referral:update_status',
      'commission:view',
      'analytics:view_all'
    ],
    'associate' => [
      'offering:view',
      'referral:view_own',
      'referral:create',
      'commission:view_own',
      'analytics:view_own'
    ],
  ];

  public function hasPermission(string $role, string $permission): bool
  {
    if ($role === 'admin') {
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
