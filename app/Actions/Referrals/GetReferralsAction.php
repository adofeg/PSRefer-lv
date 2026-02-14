<?php

namespace App\Actions\Referrals;

use App\Enums\RoleName;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetReferralsAction
{
    public function execute(User $user, array $filters = []): LengthAwarePaginator
    {
        $query = Referral::query();

        if ($user->hasRole(RoleName::Associate->value)) {
            $associate = $user->associate;
            if (! $associate) {
                // Return empty if associate profile is missing
                return $query->whereRaw('1=0')->paginate(1)->withQueryString();
            }
            $query->where('associate_id', $associate->id);
        }

        // Apply Search Filter
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->searchByClient($search);
        });

        // Apply Status Filter
        $query->when($filters['status'] ?? null, function ($query, $status) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        });

        return $query->with(['offering:id,name,base_commission', 'associate.user'])
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }
}
