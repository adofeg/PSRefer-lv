<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false; // Using custom `created_at`

    protected $fillable = [
        'entity',
        'entity_id',
        'auditable_type',
        'auditable_id',
        'action',
        'event_type',
        'actorable_type',
        'actorable_id',
        'previous_data',
        'new_data',
        'description',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'previous_data' => 'array',
            'new_data' => 'array',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actorable(): MorphTo
    {
        return $this->morphTo();
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
