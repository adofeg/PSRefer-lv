<?php

namespace App\Actions\Files;

use App\Data\Files\FileAssetData;
use App\Models\FileAsset;
use Illuminate\Support\Str;

class CreateFileAssetAction
{
    public function execute(FileAssetData $data): FileAsset
    {
        return FileAsset::create([
            'uuid' => (string) Str::uuid(),
            'disk' => $data->disk,
            'path' => $data->path,
            'original_name' => $data->original_name,
            'mime_type' => $data->mime_type,
            'size' => $data->size,
            'purpose' => $data->purpose,
            'category' => $data->category,
            'uploaded_by' => $data->uploaded_by,
            'attachable_type' => $data->attachable_type,
            'attachable_id' => $data->attachable_id,
            'metadata' => $data->metadata,
        ]);
    }
}
