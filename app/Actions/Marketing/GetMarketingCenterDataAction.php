<?php

namespace App\Actions\Marketing;

use App\Models\Category;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Support\Collection;

class GetMarketingCenterDataAction
{
    public function execute(User $user): array
    {
        $offerings = Offering::where('is_active', true)->get();
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
