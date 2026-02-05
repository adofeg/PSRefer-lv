<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Referral extends Model
{
  use HasFactory, HasUuids, SoftDeletes;

  protected $guarded = ['id'];

  protected $casts = [
    'deal_value' => 'decimal:2',
    'revenue_generated' => 'decimal:2',
    'down_payment' => 'decimal:2',
    'agency_fee' => 'decimal:2',
    'metadata' => 'array',
    'closed_at' => 'datetime',
    'paid_at' => 'datetime',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function offering()
  {
    return $this->belongsTo(Offering::class);
  }

  public function commissions()
  {
    return $this->hasMany(Commission::class);
  }
}
