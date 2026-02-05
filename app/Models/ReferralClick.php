<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralClick extends Model
{
  use HasFactory, HasUuids;

  protected $guarded = ['id'];
  public $timestamps = false; // Using custom `clicked_at`

  protected $dates = ['clicked_at'];

  public function referrer()
  {
    return $this->belongsTo(User::class, 'referrer_id');
  }

  public function offering()
  {
    return $this->belongsTo(Offering::class);
  }
}
