<?php

namespace App\Data\Settings;

use App\Enums\CurrencyCode;
use App\Enums\W9Status;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class UserSettingsData extends Data
{
    public function __construct(
        public string $name,
        public W9Status $w9_status,
        public ?array $payment_info,
        public CurrencyCode $preferred_currency,
        public ?string $category,
        public ?string $phone,
        public ?UploadedFile $logo_file,
        public ?UploadedFile $w9_file,
    ) {}
}
