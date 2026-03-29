<?php

namespace App\Data\Settings;

use App\Enums\CurrencyCode;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class UserSettingsData extends Data
{
    public function __construct(
        public string $name,
        public ?array $payment_info,
        public CurrencyCode $preferred_currency,
        public ?string $category,
        public ?string $phone,
        public ?string $payment_phone,
        public ?UploadedFile $logo_file,
        public ?UploadedFile $w9_file,
    ) {}
}
