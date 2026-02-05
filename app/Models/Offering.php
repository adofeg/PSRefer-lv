<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offering extends Model
{
  use HasFactory, HasUuids, SoftDeletes;

  protected $guarded = ['id'];

  protected $casts = [
    'base_price' => 'decimal:2',
    'base_commission' => 'decimal:2',
    'commission_rate' => 'decimal:2',
    'form_schema' => 'array',
    'commission_config' => 'array',
    'commission_rules' => 'array',
    'metadata' => 'array',
    'is_active' => 'boolean',
  ];

  public function owner()
  {
    return $this->belongsTo(User::class, 'owner_id');
  }

  public function referrals()
  {
    return $this->hasMany(Referral::class);
  }

  public function clicks()
  {
    return $this->hasMany(ReferralClick::class);
  }
}
