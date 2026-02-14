<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Support\Collection;

class GetActiveCategoryNamesAction
{
    public function execute(): Collection
    {
        return Category::where('is_active', true)
            ->orderBy('name')
            ->pluck('name');
    }
}
