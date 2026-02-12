<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offering extends Model
{
  use HasFactory, SoftDeletes;

  protected $guarded = ['id'];

  protected function casts(): array
  {
    return [
      'base_price' => 'decimal:2',
      'base_commission' => 'decimal:2',
      'commission_rate' => 'decimal:2',
      'form_schema' => 'array',
      'commission_config' => 'array',
      'commission_rules' => 'array',
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

  // Query Scopes
  public function scopeSearch($query, $term)
  {
    if (!$term) return $query;
    return $query->where(function ($q) use ($term) {
      $q->where('name', 'like', '%' . $term . '%')
        ->orWhere('description', 'like', '%' . $term . '%');
    });
  }

  public function scopeFilterByType($query, $type)
  {
    if (!$type) return $query;
    return $query->where('type', $type);
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(Category::class);
  }

  public function scopeFilterByCategory($query, $category)
  {
    if (!$category) return $query;
    return $query->where(function($q) use ($category) {
        $q->where('category', $category)
          ->orWhere('category_id', $category);
    });
  }

    public function scopeExcludeCategory($query, $category)
    {
        if (!$category) return $query;
        return $query->where(function ($q) use ($category) {
            $q->where('category', '!=', $category)
              ->orWhereNull('category');
        });
    }
}
