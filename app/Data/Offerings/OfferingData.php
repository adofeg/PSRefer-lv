<?php

namespace App\Data\Offerings;

use Spatie\LaravelData\Data;
use App\Models\Offering;

class OfferingData extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
        public ?string $description,
        public bool $is_active,
        // Add other fields as needed based on the Offering model
    ) {}

    public static function fromModel(Offering $offering): self
    {
        return new self(
            id: $offering->id,
            name: $offering->name,
            slug: $offering->slug,
            description: $offering->description,
            is_active: $offering->is_active,
        );
    }
}
