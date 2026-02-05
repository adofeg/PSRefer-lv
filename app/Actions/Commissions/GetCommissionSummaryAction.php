<?php

namespace App\Actions\Commissions;

use App\Enums\ReferralStatus;
use App\Models\Referral;
use App\Models\User;

class GetCommissionSummaryAction
{
    public function execute(User $user): array
    {
        $associate = $user->associateProfile();

        $commissions = Referral::where('associate_id', $associate?->id)
            ->whereIn('status', ReferralStatus::commissionEligible())
            ->with('offering')
            ->latest()
            ->get();

        return [
            'commissions' => $commissions,
            'totalEarned' => $commissions->sum('revenue_generated'),
            'pendingPayment' => $commissions->where('status', '!=', ReferralStatus::Paid->value)->sum('revenue_generated'),
            'paidTotal' => $commissions->where('status', ReferralStatus::Paid->value)->sum('revenue_generated'),
        ];
    }
}
