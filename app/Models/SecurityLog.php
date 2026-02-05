<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{

  protected $guarded = ['id'];
  public $timestamps = false; // Using custom `created_at`

  protected $casts = [
    'metadata' => 'array',
  ];

  public function actorable()
  {
    return $this->morphTo();
  }
}
