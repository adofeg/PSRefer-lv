<?php

namespace App\Actions\Referrals;

use App\Models\Referral;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class GetReferralsAction
{
    public function execute(): LengthAwarePaginator
    {
        $associate = Auth::user()?->associateProfile();

        return Referral::where('associate_id', $associate?->id)
            ->with('offering:id,name')
            ->latest()
            ->paginate(10);
    }
}
