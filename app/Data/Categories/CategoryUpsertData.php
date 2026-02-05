<?php

namespace App\Data\Categories;

use Spatie\LaravelData\Data;

class CategoryUpsertData extends Data
{
    public function __construct(
        public string $name,
        public ?string $description,
        public bool $is_active = true,
    ) {}
}
