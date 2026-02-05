<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Categories\CreateCategoryAction;
use App\Actions\Categories\DeleteCategoryAction;
use App\Actions\Categories\GetCategoriesAction;
use App\Actions\Categories\UpdateCategoryAction;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class CategoryController extends AdminController
{
    public function index(GetCategoriesAction $action)
    {
        return Inertia::render('Admin/Categories/Index', [
            'categories' => $action->execute()
        ]);
    }

    public function store(CategoryRequest $request, CreateCategoryAction $action)
    {
        $action->execute($request->toData());
        return back()->with('success', 'Category created successfully.');
    }

    public function update(CategoryRequest $request, Category $category, UpdateCategoryAction $action)
    {
        $action->execute($category, $request->toData());
        return back()->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category, DeleteCategoryAction $action)
    {
        try {
            $action->execute($category);
            return back()->with('success', 'Category deleted successfully.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}