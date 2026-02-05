<?php

namespace App\Actions\Referrals;

use App\Models\Referral;
use Illuminate\Support\Collection;

class GetReferralPipelineAction
{
    public function execute(): Collection
    {
        return Referral::with([
                'offering:id,name',
                'associate.user:id,name'
            ])
            ->latest()
            ->get();
    }
}