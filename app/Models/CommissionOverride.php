<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionOverride extends Model
{
    use HasFactory;

    protected $fillable = [
        'associate_id',
        'offering_id',
        'commission_rate',
        'is_active'
    ];

    public function associate(): BelongsTo
    {
        return $this->belongsTo(Associate::class);
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }
}
