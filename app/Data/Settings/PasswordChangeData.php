<?php

namespace App\Data\Settings;

use Spatie\LaravelData\Data;

class PasswordChangeData extends Data
{
    public function __construct(
        public string $current_password,
        public string $new_password,
    ) {}
}
