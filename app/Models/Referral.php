<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referral extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'associate_id',
        'offering_id',
        'client_name',
        'client_contact',
        'status',
        'deal_value',
        'revenue_generated',
        'contract_id',
        'payment_method',
        'down_payment',
        'agency_fee',
        'notes',
        'metadata',
        'closed_at',
        'paid_at',
    ];

    protected $appends = ['estimated_commission'];

    protected function casts(): array
    {
        return [
            'deal_value' => 'decimal:2',
            'revenue_generated' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'agency_fee' => 'decimal:2',
            'metadata' => 'array',
            'closed_at' => 'datetime',
            'paid_at' => 'datetime',
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

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(AuditLog::class, 'auditable_id')
            ->where('auditable_type', Referral::class)
            ->where('event_type', 'status_change')
            ->with('actorable')
            ->orderBy('created_at', 'desc');
    }

    public function getEstimatedCommissionAttribute(): string
    {
        if ($this->revenue_generated > 0) {
            return '$'.number_format($this->revenue_generated, 2);
        }

        $offering = $this->offering;
        if (! $offering) {
            return '-';
        }

        if ($offering->base_commission > 0) {
            return '$'.number_format($offering->base_commission, 2);
        }

        if ($offering->commission_rate > 0) {
            return $offering->commission_rate.'% del valor';
        }

        return '-';
    }
}
