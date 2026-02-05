<?php

namespace App\Data\Offerings;

use Spatie\LaravelData\Data;

class OfferingData extends Data
{
  public function __construct(
    public readonly ?string $id,
    public readonly string $name,
    public readonly string $type,
    public readonly ?string $category,
    public readonly ?string $description,
    public readonly ?float $base_price,
    public readonly ?float $commission_rate,
    public readonly bool $is_active = true,
    public readonly ?string $owner_id = null,
    public readonly ?array $form_schema = [],
  ) {}
}
