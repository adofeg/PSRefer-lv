<?php

namespace App\Actions\Marketing;

use App\Enums\RoleName;
use App\Models\Category;
use App\Models\Offering;
use App\Models\User;

class GetMarketingCenterDataAction
{
    public function execute(User $user): array
    {
        $offerings = Offering::where('is_active', true)
            ->when($user->hasRole(RoleName::Associate->value) && $user->category, function ($query) use ($user) {
                $category = $user->category;

                $query->where(function ($q) use ($category) {
                    $q->where(function ($q2) use ($category) {
                        $q2->whereNull('category')
                            ->orWhere('category', '!=', $category);
                    })
                        ->where(function ($q2) use ($category) {
                            $q2->whereNull('category_id')
                                ->orWhereHas('category', function ($cq) use ($category) {
                                    $cq->where('name', '!=', $category);
                                });
                        });
                });
            })
            ->get();
        $categories = Category::where('is_active', true)->get(['id', 'name']);

        return [
            'offerings' => $offerings,
            'categories' => $categories,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'category' => $user->category,
            ],
        ];
    }
}
