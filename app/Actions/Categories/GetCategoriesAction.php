<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Support\Collection;

class GetCategoriesAction
{
    public function execute(): Collection
    {
        return Category::orderBy('name')->get(['id', 'name', 'description', 'is_active']);
    }
}
