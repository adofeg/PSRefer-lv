<?php

namespace App\Data\Offerings;

use Spatie\LaravelData\Data;
use App\Models\Offering;

class OfferingData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $type,
        public ?string $category,
        public ?int $category_id,
        public ?string $description,
        public float $commission_rate,
        public bool $is_active,
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
            commission_rate: (float) $offering->commission_rate,
            is_active: (bool) $offering->is_active,
            form_schema: $offering->form_schema ?? [],
            commission_rules: $offering->commission_rules ?? [],
        );
    }
}
