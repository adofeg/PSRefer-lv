<?php

declare(strict_types=1);

namespace App\Enums;

enum AssociateRole: string
{
    case ASSOCIATE = 'associate';

    public function label(): string
    {
        return match ($this) {
            self::ASSOCIATE => 'Asociado',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $role) => $role->value, self::cases());
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(static fn (self $role) => [
            'value' => $role->value,
            'label' => $role->label(),
        ], self::cases());
    }
}
