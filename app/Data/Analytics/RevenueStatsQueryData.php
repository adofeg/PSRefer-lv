<?php

namespace App\Data\Analytics;

use Spatie\LaravelData\Data;

class RevenueStatsQueryData extends Data
{
    public function __construct(
        public ?int $associate_id,
    ) {}
}
