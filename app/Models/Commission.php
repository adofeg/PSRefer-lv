<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
  use HasFactory, HasUuids;

  protected $guarded = ['id'];

  protected $casts = [
    'amount' => 'decimal:2',
    'commission_percentage' => 'decimal:2',
    'paid_at' => 'datetime',
    'recurrence_end_date' => 'datetime',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function referral()
  {
    return $this->belongsTo(Referral::class);
  }

  public function parent()
  {
    return $this->belongsTo(Commission::class, 'parent_commission_id');
  }

  public function children()
  {
    return $this->hasMany(Commission::class, 'parent_commission_id');
  }
}
