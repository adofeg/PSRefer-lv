<?php

namespace App\Actions\Offerings;

use App\Enums\RoleName;
use App\Models\Offering;
use App\Models\User;

class GetOfferingsAction
{
    public function execute(User $user, bool $includeInactive = false, array $filters = [], bool $paginate = true): \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
    {
        $query = Offering::query();

        // Apply Search Filter
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        });

        // Apply Category Filter
        $query->when($filters['category'] ?? null, function ($query, $category) {
            $query->where('category_id', $category);
        });

        // Apply Status Filter (Only if user is allowed to see inactive)
        if ($includeInactive) {
            $query->when(isset($filters['status']) && $filters['status'] !== 'all', function ($query) use ($filters) {
                $query->where('is_active', filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN));
            });
        } else {
            // Force active for non-admins regardless of filter
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

        $query->latest();

        if ($paginate) {
            return $query->paginate(12)->withQueryString();
        }

        return $query->get();
    }
}
