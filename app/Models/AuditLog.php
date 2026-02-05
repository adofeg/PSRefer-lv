<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
  use HasFactory;

  protected $guarded = ['id'];
  public $timestamps = false; // Using custom `created_at`

  protected function casts(): array
  {
    return [
      'previous_data' => 'array',
      'new_data' => 'array',
      'metadata' => 'array',
      'old_value' => 'string',
      'new_value' => 'string',
    ];
  }

  public function actorable(): MorphTo
  {
    return $this->morphTo();
  }

  public function auditable(): MorphTo
  {
    return $this->morphTo();
  }
}
