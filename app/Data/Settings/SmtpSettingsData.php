<?php

namespace App\Data\Settings;

use Spatie\LaravelData\Data;

class SmtpSettingsData extends Data
{
    public function __construct(
        public string $host,
        public string $port,
        public string $username,
        public string $password,
        public string $encryption,
        public string $from_address,
        public string $from_name,
    ) {}
}
