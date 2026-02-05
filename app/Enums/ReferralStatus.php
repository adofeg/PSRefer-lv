<?php

namespace App\Enums;

enum ReferralStatus: string
{
    case Prospect = 'Prospecto';
    case Closed = 'Cerrado';
    case Won = 'Ganado';
    case Paid = 'Pagado';

    public static function commissionEligible(): array
    {
        return [self::Closed->value, self::Won->value, self::Paid->value];
    }
}
