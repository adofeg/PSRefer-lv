<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
  use HasUuids;

  protected $guarded = ['id'];
  public $timestamps = false; // Using custom `created_at`

  protected $casts = [
    'metadata' => 'array',
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
