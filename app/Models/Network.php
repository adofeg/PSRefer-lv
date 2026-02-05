<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Network extends Model
{
  use HasFactory;

  protected $fillable = [
    'parent_associate_id',
    'child_associate_id',
    'level',
    'total_sales',
  ];

  protected function casts(): array
  {
    return [
      'level' => 'integer',
      'total_sales' => 'decimal:2',
    ];
  }

  public function parent(): BelongsTo
  {
    return $this->belongsTo(Associate::class, 'parent_associate_id');
  }

  public function child(): BelongsTo
  {
    return $this->belongsTo(Associate::class, 'child_associate_id');
  }
}
