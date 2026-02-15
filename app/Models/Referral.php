<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'status',
        'deal_value',
        'revenue_generated',
        'contract_id',
        'payment_method',
        'down_payment',
        'agency_fee',
        'notes',
        'consent_confirmed',
        'metadata',
        'closed_at',
        'paid_at',
    ];

    protected $appends = ['client_name', 'client_contact', 'client_email', 'client_phone', 'estimated_commission'];

    protected function casts(): array
    {
        return [
            'deal_value' => 'decimal:2',
            'revenue_generated' => 'decimal:2',
            'down_payment' => 'decimal:2',
            'agency_fee' => 'decimal:2',
            'metadata' => 'array',
            'consent_confirmed' => 'boolean',
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

    public function scopeSearchByClient(Builder $query, string $search): Builder
    {
        $term = trim($search);
        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term): void {
            $q->where('metadata->client_name', 'like', "%{$term}%")
                ->orWhere('metadata->client_contact', 'like', "%{$term}%")
                ->orWhere('metadata->client_email', 'like', "%{$term}%")
                ->orWhere('metadata->client_phone', 'like', "%{$term}%");
        });
    }

    public function getClientNameAttribute(): ?string
    {
        $metadata = $this->metadata ?? [];

        if (! is_array($metadata)) {
            return "Referido #{$this->id}";
        }

        return $metadata['client_name']
            ?? $metadata['full_name']
            ?? $metadata['name']
            ?? "Referido #{$this->id}";
    }

    public function setClientNameAttribute(?string $value): void
    {
        $this->setMetadataField('client_name', $value);
    }

    public function getClientContactAttribute(): ?string
    {
        $metadata = $this->metadata ?? [];

        if (! is_array($metadata)) {
            return null;
        }

        // Prioritize explicit client_contact or construct from parts
        if (! empty($metadata['client_contact'])) {
            return (string) $metadata['client_contact'];
        }

        $email = $metadata['client_email'] ?? null;
        $phone = $metadata['client_phone'] ?? null;

        if ($email && $phone) {
            return "{$email} / {$phone}";
        }

        return $email ?: $phone ?: null;
    }

    public function setClientContactAttribute(?string $value): void
    {
        $this->setMetadataField('client_contact', $value);
    }

    public function getClientEmailAttribute(): ?string
    {
        $metadata = $this->metadata ?? [];

        if (! is_array($metadata)) {
            return null;
        }

        return $metadata['client_email'] ?? null;
    }

    public function getClientPhoneAttribute(): ?string
    {
        $metadata = $this->metadata ?? [];

        if (! is_array($metadata)) {
            return null;
        }

        return $metadata['client_phone'] ?? null;
    }

    protected function setMetadataField(string $key, ?string $value): void
    {
        $metadata = $this->metadata ?? [];
        if (! is_array($metadata)) {
            $metadata = [];
        }

        if ($value === null || trim($value) === '') {
            unset($metadata[$key]);
        } else {
            $metadata[$key] = $value;
        }

        $this->attributes['metadata'] = json_encode($metadata);
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
            if ($offering->commission_type === 'fixed') {
                return '$'.number_format($offering->base_commission, 2);
            }
            return $offering->base_commission.'% del valor';
        }

        return '-';
    }
}
