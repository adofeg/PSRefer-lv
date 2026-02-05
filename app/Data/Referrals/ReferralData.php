<?php

namespace App\Data\Referrals;

use Spatie\LaravelData\Data;

class ReferralData extends Data
{
  public function __construct(
    public readonly string $client_name,
    public readonly string $client_contact,
    public readonly string $offering_id,
    public readonly ?string $status = 'Prospecto',
    public readonly ?array $metadata = [],
    public readonly ?string $notes = null,
    public readonly ?string $user_id = null, // Injected by Controller
  ) {}
}
