<?php

namespace App\Data\Auth;

use Spatie\LaravelData\Data;

class UserData extends Data
{
  public function __construct(
    public readonly string $name,
    public readonly string $email,
    public readonly string $password,
    public readonly ?string $phone = null,
    public readonly string $role = 'associate',
    public readonly ?string $category = null,
    public readonly ?string $referred_by_id = null,
    public readonly ?string $offering_id = null, // Logic specific: auto-subscribe
  ) {}
}
