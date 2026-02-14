<?php

namespace App\Data\Offerings;

use Spatie\LaravelData\Data;

class OfferingUpsertData extends Data
{
    public function __construct(
        public string $name,
        public ?int $category_id,
        public ?string $category,
        public ?string $type,
        public ?float $base_commission,
        public ?string $description,
        public ?float $base_price,
        public ?float $commission_rate,
        public ?array $form_schema,
        public ?array $commission_config,
        public ?array $commission_rules,
        public ?bool $is_active = null,
    ) {}
}
