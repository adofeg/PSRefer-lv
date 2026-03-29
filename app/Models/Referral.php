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
        'contract_id',
        'notes',
        'consent_confirmed',
        'metadata',
        'closed_at',
        'paid_at',
        'reminder_date',
        'sector_id',
    ];

    protected $appends = ['client_name', 'client_contact', 'client_email', 'client_phone', 'estimated_commission'];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'consent_confirmed' => 'boolean',
            'closed_at' => 'datetime',
            'paid_at' => 'datetime',
            'reminder_date' => 'date',
        ];
    }

    public function associate(): BelongsTo
    {
        return $this->belongsTo(Associate::class);
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(Offering::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(Commission::class);
    }

    public function fileAssets(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(FileAsset::class, 'attachable');
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
        // If closed/paid, show the ACTUAL commission if calculated
        if ($this->status === 'Cerrado' || $this->status === 'Pagado') {
            $total = $this->commissions()->sum('amount');
            if ($total > 0) {
                return '$'.number_format($total, 2);
            }

            // If $0, show the type as a label instead of 0
            $commission = $this->commissions()->first();
            if ($commission) {
                if ($commission->commission_type === 'percentage') {
                    return 'Porcentaje';
                }
                if ($commission->commission_type === 'variable') {
                    return 'Variable';
                }
            }
        }

        // Pre-closure or fallback: show the rule from offering
        $offering = $this->offering;
        if ($offering) {
            if ($offering->commission_type->value === 'fixed' || $offering->commission_type === 'fixed') {
                return '$'.number_format($offering->base_commission ?? 0, 2);
            }
            if ($offering->commission_type->value === 'percentage' || $offering->commission_type === 'percentage') {
                return ($offering->base_commission ?? 0).'%';
            }

            return 'Variable';
        }

        return '-';
    }
}
