<?php

namespace App\Data\Referrals;

use App\Enums\ReferralStatus;
use Spatie\LaravelData\Data;

class ReferralData extends Data
{
    public function __construct(
        public readonly string $client_name,
        public readonly string $client_contact,
        public readonly int $offering_id,
        public readonly ?ReferralStatus $status = ReferralStatus::Prospect,
        public readonly ?array $metadata = [],
        public readonly ?string $notes = null,
        public readonly ?int $associate_id = null,
    ) {}
}
