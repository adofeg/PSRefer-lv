<?php

namespace App\Data\Commissions;

use Spatie\LaravelData\Data;

class CommissionOverrideQueryData extends Data
{
    public function __construct(
        public int $associate_id,
    ) {}
}
