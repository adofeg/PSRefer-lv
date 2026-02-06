<?php

namespace App\Data\Offerings;

use Spatie\LaravelData\Data;
use App\Models\Offering;

class OfferingData extends Data
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public string $type = 'service',
        public ?string $category = null,
        public ?int $category_id = null,
        public ?string $description = null,
        public ?float $base_price = null,
        public ?float $base_commission = null,
        public ?float $commission_rate = null,
        public bool $is_active = true,
        public ?array $form_schema = [],
        public ?array $commission_rules = [],
    ) {}

    public static function fromModel(Offering $offering): self
    {
        return new self(
            id: (int) $offering->id,
            name: $offering->name,
            type: $offering->type,
            category: $offering->category_id ? $offering->category?->name : $offering->category,
            category_id: $offering->category_id ? (int) $offering->category_id : null,
            description: $offering->description,
            base_price: $offering->base_price !== null ? (float) $offering->base_price : null,
            base_commission: $offering->base_commission !== null ? (float) $offering->base_commission : null,
            commission_rate: $offering->commission_rate !== null ? (float) $offering->commission_rate : null,
            is_active: (bool) $offering->is_active,
            form_schema: $offering->form_schema ?? [],
            commission_rules: $offering->commission_rules ?? [],
        );
    }
}
