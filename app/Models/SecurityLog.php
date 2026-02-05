<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SecurityLog extends Model
{
  use HasFactory;

  protected $guarded = ['id'];
  public $timestamps = false; // Using custom `created_at`

  protected function casts(): array
  {
    return [
      'metadata' => 'array',
    ];
  }

  public function actorable(): MorphTo
  {
    return $this->morphTo();
  }
}
