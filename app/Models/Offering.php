<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offering extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'owner_employee_id',
        'type',
        'category',
        'category_id',
        'name',
        'description',
        'base_commission',
        'commission_type',
        'form_schema',
        'commission_config',
        'commission_rules',
        'is_active',
        'metadata',
        'notification_emails',
    ];

    protected function casts(): array
    {
        return [
            'base_commission' => 'decimal:2',
            'commission_type' => 'string',
            'form_schema' => 'array',
            'commission_config' => 'array',
            'commission_rules' => 'array',
            'notification_emails' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $offering): void {
            if (empty($offering->form_schema) || ! is_array($offering->form_schema)) {
                $offering->form_schema = app(\App\Services\OfferingSchemaService::class)->getDefaultSchema();
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'owner_employee_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(ReferralClick::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Query scopes
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', '%'.$term.'%')
                ->orWhere('description', 'like', '%'.$term.'%');
        });
    }

    public function scopeFilterByType(Builder $query, ?string $type): Builder
    {
        if (! $type) {
            return $query;
        }

        return $query->where('type', $type);
    }

    public function scopeFilterByCategory(Builder $query, string|int|null $category): Builder
    {
        if (! $category) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($category) {
            $q->where('category', $category)
                ->orWhere('category_id', $category);
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeExcludeCategory(Builder $query, string|int|null $category): Builder
    {
        if (! $category) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($category) {
            // Check string column
            $q->where(function ($sq) use ($category) {
                $sq->whereNull('category')
                    ->orWhere('category', '!=', $category);
            });

            // Check relationship if category_id exists
            if (is_numeric($category)) {
                $q->where(function ($sq) use ($category) {
                    $sq->whereNull('category_id')
                        ->orWhere('category_id', '!=', $category);
                });
            } else {
                $q->where(function ($sq) use ($category) {
                    $sq->whereNull('category_id')
                        ->orWhereHas('category', function ($cq) use ($category) {
                            $cq->where('name', '!=', $category);
                        });
                });
            }
        });
    }
}
