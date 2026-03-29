<?php

namespace App\Actions\Referrals;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Collection;

class GetReferralPipelineAction
{
    public function execute(User $user, array $filters = []): Collection
    {
        $query = Referral::with([
            'offering.category:id,name',
            'associate.user:id,name,profileable_id,profileable_type',
        ])->latest();

        if ($user->isAssociate()) {
            $associate = $user->associate;
            if ($associate) {
                $query->where('associate_id', $associate->id);
            } else {
                return collect(); // Return empty if no associate profile
            }
        }

        // Apply Category Filter
        if (! empty($filters['category_id'])) {
            $query->whereHas('offering', function ($q) use ($filters) {
                $q->where('category_id', $filters['category_id']);
            });
        }

        // Apply Sector Filter
        if (! empty($filters['sector_id'])) {
            $query->where('sector_id', $filters['sector_id']);
        }

        return $query->get();
    }
}
