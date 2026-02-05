<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Validation\ValidationException;

class DeleteCategoryAction
{
    public function execute(Category $category): void
    {
        if ($category->offerings()->count() > 0) {
            throw ValidationException::withMessages([
                'category' => 'Cannot delete category with associated offerings.'
            ]);
        }

        $category->delete();
    }
}
