<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralClick extends Model
{
  use HasFactory;

  protected $guarded = ['id'];
  public $timestamps = false; // Using custom `clicked_at`

  protected $dates = ['clicked_at'];

  public function referrer()
  {
    return $this->belongsTo(Associate::class, 'referrer_associate_id');
  }

  public function offering()
  {
    return $this->belongsTo(Offering::class);
  }
}
