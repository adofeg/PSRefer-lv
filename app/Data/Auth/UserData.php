<?php

namespace App\Data\Auth;

use App\Enums\RoleName;
use Spatie\LaravelData\Data;

class UserData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $phone = null,
        public readonly string $role = RoleName::Associate->value,
        public readonly ?string $category = null,
        public readonly ?int $referrer_id = null,
        public readonly ?int $offering_id = null, // Logic specific: auto-subscribe
    ) {}
}
