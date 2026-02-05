<?php

namespace App\Data\Public;

use Spatie\LaravelData\Data;

class OfferingApplicationData extends Data
{
    public function __construct(
        public string $client_name,
        public string $client_contact,
        public ?int $referrer_id,
        public ?array $form_data,
        public ?string $notes,
    ) {}
}
