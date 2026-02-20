<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileAsset extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'uuid',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'checksum',
        'purpose',
        'category',
        'is_primary',
        'metadata',
        'uploaded_by',
        'attachable_type',
        'attachable_id',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function attachable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
}
