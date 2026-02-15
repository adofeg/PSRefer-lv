<?php

namespace App\Data\Referrals;

use App\Enums\ReferralStatus;
use Spatie\LaravelData\Data;

class ReferralData extends Data
{
    public function __construct(
        public readonly int $offering_id,
        public readonly ?ReferralStatus $status = ReferralStatus::Prospect,
        public readonly array $metadata = [],
        public readonly ?string $notes = null,
        public readonly ?int $associate_id = null,
        public readonly ?string $client_name = null,
        public readonly ?string $client_email = null,
        public readonly ?string $client_phone = null,
    ) {}
}
