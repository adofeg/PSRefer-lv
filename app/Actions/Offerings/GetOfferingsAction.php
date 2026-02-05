<?php

namespace App\Actions\Offerings;

use App\Enums\RoleName;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class GetOfferingsAction
{
    public function execute(User $user, bool $includeInactive = false): LengthAwarePaginator
    {
        $query = Offering::query();

        if (!$includeInactive) {
            $query->where('is_active', true);
        }

        if ($user->hasRole(RoleName::Associate->value)) {
            $category = $user->category;

            if ($category) {
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
            }
        }

        return $query->latest()->paginate(12);
    }
}
