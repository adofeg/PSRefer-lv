<?php

namespace App\Enums;

enum CommissionType: string
{
    case Fixed = 'fixed';
    case Percentage = 'percentage';
    case Manual = 'manual';
    case Variable = 'variable';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fija',
            self::Percentage => 'Porcentaje',
            self::Manual => 'Manual',
            self::Variable => 'Variable',
        };
    }
}
