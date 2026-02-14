<?php

namespace App\Actions\Analytics;

use App\Enums\CommissionStatus;
use App\Enums\ReferralStatus;
use App\Enums\RoleName;
use App\Models\Commission;
use App\Models\Referral;
use App\Models\User;

class GetRevenueStatsAction
{
    public function execute(User $user, ?int $targetAssociateId): array
    {
        if ($user->hasRole(RoleName::Associate->value)) {
            $targetAssociateId = $user->associate?->id;
        }

        $revenueQuery = Referral::where('status', ReferralStatus::Closed->value);
        $commissionsQuery = Commission::where('status', CommissionStatus::Paid->value);

        if ($targetAssociateId) {
            $revenueQuery->where('associate_id', $targetAssociateId);
            $commissionsQuery->where('associate_id', $targetAssociateId);
        }

        $totalRevenue = $revenueQuery->sum('revenue_generated');
        $totalDeals = $revenueQuery->count();
        $totalCommissions = $commissionsQuery->sum('amount');

        return [
            'total_revenue' => (float) $totalRevenue,
            'total_deals' => (int) $totalDeals,
            'total_commissions' => (float) $totalCommissions,
        ];
    }
}
