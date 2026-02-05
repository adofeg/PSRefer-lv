<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{

  protected $guarded = ['id'];
  public $timestamps = false; // Using custom `created_at`

  protected $casts = [
    'previous_data' => 'array',
    'new_data' => 'array',
    'metadata' => 'array',
    'old_value' => 'string',
    'new_value' => 'string',
  ];

  public function actorable()
  {
    return $this->morphTo();
  }

  public function auditable()
  {
    return $this->morphTo();
  }
}
