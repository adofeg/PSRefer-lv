<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'referral_id',
        'associate_id',
        'parent_commission_id',
        'amount',
        'commission_percentage',
        'commission_type',
        'status',
        'paid_at',
        'recurrence_type',
        'recurrence_interval',
        'recurrence_end_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'commission_percentage' => 'decimal:2',
            'paid_at' => 'datetime',
            'recurrence_end_date' => 'datetime',
        ];
    }

    public function associate(): BelongsTo
    {
        return $this->belongsTo(Associate::class);
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Commission::class, 'parent_commission_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Commission::class, 'parent_commission_id');
    }
}
