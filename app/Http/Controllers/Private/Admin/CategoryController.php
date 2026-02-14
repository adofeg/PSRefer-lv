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
    public function __construct()
    {
        $this->authorizeResource(Category::class, 'category');
    }

    public function index(CategoryRequest $request, GetCategoriesAction $action)
    {
        $filters = $request->only(['search', 'status']);
        $categories = $action->execute($filters);

        return Inertia::render('Private/Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => $filters
        ]);
    }

    public function store(CategoryRequest $request, CreateCategoryAction $action)
    {
        $action->execute($request->toData());
        return back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(CategoryRequest $request, Category $category, UpdateCategoryAction $action)
    {
        $action->execute($category, $request->toData());
        return back()->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Category $category, DeleteCategoryAction $action)
    {
        try {
            $action->execute($category);
            return back()->with('success', 'Categoría eliminada correctamente.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleStatus(CategoryRequest $request, Category $category, UpdateCategoryAction $action)
    {
        $this->authorize('update', $category);

        $isActive = (bool) $request->boolean('is_active');
        
        // Manual update locally since UpdateCategoryAction expects full DTO
        // Ideally we should add updateStatus to Action like in Offering
        $category->update(['is_active' => $isActive]);

        return back()->with('success', $isActive ? 'Categoría activada correctamente.' : 'Categoría desactivada correctamente.');
    }
}
