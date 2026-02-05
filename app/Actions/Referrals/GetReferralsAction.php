<?php

namespace App\Actions\Referrals;

use App\Enums\RoleName;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetReferralsAction
{
    public function execute(User $user): LengthAwarePaginator
    {
        $query = Referral::query();

        if ($user->hasRole(RoleName::Associate->value)) {
            $associate = $user->associateProfile();
            $query->where('associate_id', $associate?->id);
        }

        return $query->with('offering:id,name')
            ->latest()
            ->paginate(10);
    }
}
