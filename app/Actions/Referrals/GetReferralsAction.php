<?php

namespace App\Actions\Referrals;

use App\Models\Referral;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class GetReferralsAction
{
    public function execute(): LengthAwarePaginator
    {
        return Referral::where('user_id', Auth::id())
            ->with('offering:id,name')
            ->latest()
            ->paginate(10);
    }
}
