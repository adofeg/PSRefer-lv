<?php

namespace App\Enums;

enum RoleName: string
{
    case Admin = 'admin';
    case PsAdmin = 'psadmin';
    case Associate = 'associate';

    public static function adminRoles(): array
    {
        return [self::Admin->value, self::PsAdmin->value];
    }

    public static function adminOrAssociate(): array
    {
        return [self::Admin->value, self::PsAdmin->value, self::Associate->value];
    }

    public static function isAdmin($user): bool
    {
        return $user->hasRole(self::adminRoles());
    }
}
