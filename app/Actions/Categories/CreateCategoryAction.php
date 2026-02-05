<?php

namespace App\Actions\Categories;

use App\Data\Categories\CategoryUpsertData;
use App\Models\Category;

class CreateCategoryAction
{
    public function execute(CategoryUpsertData $data): Category
    {
        return Category::create([
            'name' => $data->name,
            'description' => $data->description,
            'is_active' => $data->is_active,
        ]);
    }
}
