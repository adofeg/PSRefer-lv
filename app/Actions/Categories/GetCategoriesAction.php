<?php

namespace App\Actions\Categories;

use App\Models\Category;
use Illuminate\Support\Collection;

class GetCategoriesAction
{
    public function execute(array $filters = []): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Category::query();

        // Apply Search
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        });

        // Apply Status
        $query->when($filters['status'] ?? null, function ($query, $status) {
            if ($status === 'true') {
                $query->where('is_active', true);
            } elseif ($status === 'false') {
                $query->where('is_active', false);
            }
        });

        return $query->orderBy('name')->paginate(10)->withQueryString();
    }
}
