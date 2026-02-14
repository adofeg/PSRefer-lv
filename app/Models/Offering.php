<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
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
        'base_price',
        'base_commission',
        'commission_rate',
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
            'base_price' => 'decimal:2',
            'base_commission' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'form_schema' => 'array',
            'commission_config' => 'array',
            'commission_rules' => 'array',
            'notification_emails' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
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
        if (!$term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
                ->orWhere('description', 'like', '%' . $term . '%');
        });
    }

    public function scopeFilterByType(Builder $query, ?string $type): Builder
    {
        if (!$type) {
            return $query;
        }

        return $query->where('type', $type);
    }

    public function scopeFilterByCategory(Builder $query, string|int|null $category): Builder
    {
        if (!$category) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($category) {
            $q->where('category', $category)
                ->orWhere('category_id', $category);
        });
    }

    public function scopeExcludeCategory(Builder $query, ?string $category): Builder
    {
        if (!$category) {
            return $query;
        }

        return $query->where(function ($q) use ($category) {
            $q->where('category', '!=', $category)
                ->orWhereNull('category');
        });
    }
}
