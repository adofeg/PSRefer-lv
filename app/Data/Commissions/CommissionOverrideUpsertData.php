<?php

namespace App\Data\Commissions;

use Spatie\LaravelData\Data;

class CommissionOverrideUpsertData extends Data
{
    public function __construct(
        public int $associate_id,
        public int $offering_id,
        public float $commission_rate,
    ) {}
}
