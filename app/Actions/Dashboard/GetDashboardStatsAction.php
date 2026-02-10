<?php

namespace App\Actions\Dashboard;

use App\Enums\ReferralStatus;
use App\Enums\RoleName;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;

class GetDashboardStatsAction
{
    public function execute(User $user): array
    {
        // 1. Context: Admin vs Associate
        if ($user->hasRole(RoleName::adminRoles())) {
            $referralsQuery = Referral::query();
            $closedReferralsQuery = Referral::where('status', ReferralStatus::Closed->value);
            
            // Admin Specific Stats
            $totalRevenue = Referral::where('status', ReferralStatus::Closed->value)->sum('revenue_generated') ?? 0;
            $totalCommissionsPaid = \App\Models\Commission::where('status', \App\Enums\CommissionStatus::Paid->value)->sum('amount');
            $pendingReferralsCount = Referral::where('status', ReferralStatus::Prospect->value)->count();
            $totalUsers = User::count();
        } else {
            $associate = $user->associateProfile();
            $referralsQuery = Referral::where('associate_id', $associate?->id);
            $closedReferralsQuery = Referral::where('associate_id', $associate?->id)->where('status', ReferralStatus::Closed->value);
            
            // Personal Revenue Contribution
            $totalRevenue = $closedReferralsQuery->sum('revenue_generated') ?? 0;
            $totalCommissionsPaid = 0; // Not used for associate view in same way
            $pendingReferralsCount = 0;
            $totalUsers = 0;
        }

        // 2. Earnings (Always Personal Balance)
        $totalEarnings = $user->balance ?? 0;

        // 3. Recent Activity (Last 5)
        $recentReferrals = $referralsQuery->with('offering')
            ->latest()
            ->take(5)
            ->get();

        // 4. Chart Data (Monthly Revenue)
        $chartData = $closedReferralsQuery->clone()
            ->whereYear('updated_at', date('Y'))
            ->get(['updated_at', 'revenue_generated']);

        $monthlyRevenue = $chartData->groupBy(function ($item) {
            return $item->updated_at ? Carbon::parse($item->updated_at)->format('m') : '00';
        })->map(function ($group) {
            return $group->sum('revenue_generated');
        });

        // Ensure all months are present for chart
        $revenueSeries = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $revenueSeries[] = $monthlyRevenue->get($month, 0);
        }

        return [
            'stats' => [
                'current_balance' => (float) $totalEarnings,
                'total_revenue' => (float) $totalRevenue,
                'total_commissions_paid' => (float) $totalCommissionsPaid,
                'pending_referrals' => $pendingReferralsCount,
                'total_users' => $totalUsers,
                'is_admin' => $user->hasRole(RoleName::adminRoles()),
            ],
            'recentReferrals' => $recentReferrals,
            'monthlyRevenue' => $revenueSeries,
            'accountManager' => $user->associateProfile()?->referrer?->user ? [
                'name' => $user->associateProfile()->referrer->user->name,
                'email' => $user->associateProfile()->referrer->user->email,
                'phone' => $user->associateProfile()->referrer->user->phone,
                'logo_url' => $user->associateProfile()->referrer->user->logo_url,
            ] : null
        ];
    }
}
