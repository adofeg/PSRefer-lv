<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionOverride extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'associate_id',
        'offering_id',
        'base_commission',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_commission' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function associate(): BelongsTo
    {
        return $this->belongsTo(Associate::class);
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }
}
