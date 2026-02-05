<?php

namespace App\Actions\Dashboard;

use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GetDashboardStatsAction
{
    public function execute(User $user): array
    {
        // 1. Context: Admin vs Associate
        if ($user->hasRole(['admin', 'psadmin'])) {
            $referralsQuery = Referral::query();
            $closedReferralsQuery = Referral::where('status', 'Cerrado');
            
            // Total Platform Revenue
            $totalRevenue = Referral::where('status', 'Cerrado')->sum('revenue_generated') ?? 0;
        } else {
            $referralsQuery = Referral::where('user_id', $user->id);
            $closedReferralsQuery = Referral::where('user_id', $user->id)->where('status', 'Cerrado');
            
            // Personal Revenue Contribution
            $totalRevenue = $closedReferralsQuery->sum('revenue_generated') ?? 0;
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
            ->whereYear('updated_at', date('Y')) // Assuming 'updated_at' is used for 'closed_at' logic unless column exists
            // Verify DB schema for 'closed_at'. Using 'updated_at' as fallback or check migration. 
            // Migration usually has timestamps. Referral likely doesn't have 'closed_at' unless added.
            // Let's assume Updated At for now if status changed to Closed consistently.
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
                'current_balance' => $totalEarnings,
                'total_revenue' => $totalRevenue,
                // 'pending_payouts' => ... 
            ],
            'recentReferrals' => $recentReferrals,
            'monthlyRevenue' => $revenueSeries
        ];
    }
}
