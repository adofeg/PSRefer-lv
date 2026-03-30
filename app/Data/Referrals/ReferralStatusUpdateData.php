<?php

namespace App\Data\Referrals;

use App\Enums\ReferralStatus;
use Spatie\LaravelData\Data;

class ReferralStatusUpdateData extends Data
{
    public function __construct(
        public ReferralStatus $status,
        public ?string $contract_id = null,
        public ?string $notes = null,
        public ?string $reminder_date = null,
    ) {}
}
