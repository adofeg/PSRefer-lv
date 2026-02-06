<?php

namespace App\Enums;

enum ReferralStatus: string
{
    case Prospect = 'Prospecto';
    case Contacted = 'Contactado';
    case InProcess = 'En Proceso';
    case Closed = 'Cerrado';
    case Lost = 'Perdido';
    case Paid = 'Pagado';

    public static function commissionEligible(): array
    {
        return [self::Closed->value, self::Paid->value];
    }
}
