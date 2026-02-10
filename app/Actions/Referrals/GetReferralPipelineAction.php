<?php

namespace App\Actions\Referrals;

use App\Models\Referral;
use Illuminate\Support\Collection;

class GetReferralPipelineAction
{
    public function execute(\App\Models\User $user): Collection
    {
        $query = Referral::with([
            'offering:id,name',
            'associate.user:id,name'
        ])->latest();

        if ($user->hasRole(\App\Enums\RoleName::Associate->value)) {
            $associate = $user->associateProfile;
            if ($associate) {
                $query->where('associate_id', $associate->id);
            }
        }

        return $query->get();
    }
}