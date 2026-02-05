<?php

namespace App\Data\Referrals;

use App\Enums\ReferralStatus;
use Spatie\LaravelData\Data;

class ReferralStatusUpdateData extends Data
{
    public function __construct(
        public ReferralStatus $status,
        public ?float $deal_value = null,
        public ?float $revenue_generated = null,
        public ?string $contract_id = null,
        public ?string $payment_method = null,
        public ?float $down_payment = null,
        public ?float $agency_fee = null,
        public ?string $notes = null,
    ) {}
}
