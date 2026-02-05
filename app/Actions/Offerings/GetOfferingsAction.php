<?php

namespace App\Actions\Offerings;

use App\Models\Offering;
use Illuminate\Pagination\LengthAwarePaginator;

class GetOfferingsAction
{
  public function execute(?string $category = null, ?string $type = null): LengthAwarePaginator
  {
    $query = Offering::query()
      ->where('is_active', true)
      ->with('owner:id,name,logo_url');

    if ($category) {
      $query->where('category', $category);
    }

    if ($type) {
      $query->where('type', $type);
    }

    return $query->latest()->paginate(12);
  }
}
