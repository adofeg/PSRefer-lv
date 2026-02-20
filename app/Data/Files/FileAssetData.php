<?php

namespace App\Data\Files;

use Spatie\LaravelData\Data;

class FileAssetData extends Data
{
    public function __construct(
        public string $disk,
        public string $path,
        public string $original_name,
        public ?string $mime_type,
        public ?int $size,
        public string $purpose,
        public string $category,
        public ?int $uploaded_by,
        public ?string $attachable_type = null,
        public ?int $attachable_id = null,
        public ?array $metadata = null,
    ) {}
}
