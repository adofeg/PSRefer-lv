<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Support\Collection;

class GetActiveCategoriesAction
{
    public function execute(): Collection
    {
        return Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
