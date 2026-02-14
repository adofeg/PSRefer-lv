<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralClick extends Model
{
    use HasFactory;

    public $timestamps = false; // Using custom `clicked_at`

    protected $fillable = [
        'referrer_associate_id',
        'offering_id',
        'link_type',
        'user_agent',
        'ip_address',
        'clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'clicked_at' => 'datetime',
        ];
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Associate::class, 'referrer_associate_id');
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }
}
