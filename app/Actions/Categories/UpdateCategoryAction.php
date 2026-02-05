<?php

namespace App\Actions\Categories;

use App\Data\Categories\CategoryUpsertData;
use App\Models\Category;

class UpdateCategoryAction
{
    public function execute(Category $category, CategoryUpsertData $data): Category
    {
        $category->update([
            'name' => $data->name,
            'description' => $data->description,
            'is_active' => $data->is_active,
        ]);

        return $category->refresh();
    }
}
