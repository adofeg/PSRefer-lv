<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Associate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'balance',
        'category',
        'payment_info',
        'payment_phone',
        'referrer_id',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'payment_info' => 'array',
        ];
    }

    // Morph Parent
    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'profileable');
    }

    // MLM
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Associate::class, 'referrer_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    // Associate-specific logic that used to be on User

    public function w9(): MorphOne
    {
        return $this->morphOne(FileAsset::class, 'attachable')->where('purpose', 'w9')->latest();
    }

    public function getW9FileUrlAttribute()
    {
        return $this->w9?->path ? '/storage/'.$this->w9->path : null;
    }
}
