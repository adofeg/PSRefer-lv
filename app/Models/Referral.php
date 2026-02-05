<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\AuditLog;

class Referral extends Model
{
  use HasFactory, SoftDeletes;

  protected $guarded = ['id'];

  protected $casts = [
    'deal_value' => 'decimal:2',
    'revenue_generated' => 'decimal:2',
    'down_payment' => 'decimal:2',
    'agency_fee' => 'decimal:2',
    'total_payment' => 'decimal:2',
    'metadata' => 'array',
    'closed_at' => 'datetime',
    'paid_at' => 'datetime',
  ];

  public function associate()
  {
    return $this->belongsTo(Associate::class);
  }

  public function offering()
  {
    return $this->belongsTo(Offering::class);
  }

  public function commissions()
  {
    return $this->hasMany(Commission::class);
  }

  public function history()
  {
    return $this->hasMany(AuditLog::class, 'auditable_id')
      ->where('auditable_type', Referral::class)
      ->where('event_type', 'status_change')
      ->with('actorable')
      ->orderBy('created_at', 'desc');
  }
}
