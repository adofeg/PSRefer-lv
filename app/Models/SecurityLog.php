<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SecurityLog extends Model
{
    use HasFactory;

    public $timestamps = false; // Using custom `created_at`

    protected $fillable = [
        'event_type',
        'actorable_type',
        'actorable_id',
        'email',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function actorable(): MorphTo
    {
        return $this->morphTo();
    }
}
