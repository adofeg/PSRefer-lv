<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Network extends Model
{

  protected $fillable = [
    'parent_associate_id',
    'child_associate_id',
    'level',
    'total_sales',
  ];

  protected $casts = [
    'level' => 'integer',
    'total_sales' => 'decimal:2',
  ];

  public function parent()
  {
    return $this->belongsTo(Associate::class, 'parent_associate_id');
  }

  public function child()
  {
    return $this->belongsTo(Associate::class, 'child_associate_id');
  }
}
