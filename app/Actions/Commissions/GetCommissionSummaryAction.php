<?php

namespace App\Actions\Commissions;

use App\Enums\ReferralStatus;
use App\Models\Referral;
use App\Models\User;

class GetCommissionSummaryAction
{
    public function execute(User $user): array
    {
        $associate = $user->associate;

        $referrals = Referral::where('associate_id', $associate?->id)
            ->whereIn('status', ReferralStatus::commissionEligible())
            ->with(['offering', 'commissions' => function ($q) use ($associate) {
                $q->where('associate_id', $associate?->id);
            }])
            ->latest()
            ->get();

        // Calculate totals based on actual commission records
        $calculateEarnings = fn ($referral) => (float) $referral->commissions->sum('amount');

        return [
            'commissions' => $referrals,
            'totalEarned' => $referrals->where('status', ReferralStatus::Paid->value)->sum($calculateEarnings),
            'pendingPayment' => $referrals->where('status', '!=', ReferralStatus::Paid->value)->sum($calculateEarnings),
            'paidTotal' => $referrals->where('status', ReferralStatus::Paid->value)->sum($calculateEarnings),
        ];
    }
}
