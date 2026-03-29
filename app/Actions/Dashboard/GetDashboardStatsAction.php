<?php

namespace App\Actions\Dashboard;

use App\Enums\CommissionStatus;
use App\Enums\ReferralStatus;
use App\Models\Associate;
use App\Models\Commission;
use App\Models\Offering;
use App\Models\Referral;
use App\Models\User;
use Carbon\Carbon;

class GetDashboardStatsAction
{
    public function execute(User $user): array
    {
        $now = Carbon::now();

        // 1. Context: Admin vs Associate
        if ($user->isAdmin() || $user->isEmployee()) {
            $referralsQuery = Referral::query();

            // Admin Specific Stats
            $totalCommissionsPaid = Commission::where('status', CommissionStatus::Paid->value)->sum('amount');
            $totalCommissionsPending = Commission::where('status', CommissionStatus::Pending->value)->sum('amount');
            $totalCommissionsGenerated = $totalCommissionsPaid + $totalCommissionsPending;

            $pendingReferralsCount = Referral::whereIn('status', [
                ReferralStatus::Prospect->value,
                ReferralStatus::Contacted->value,
                ReferralStatus::InProcess->value,
                ReferralStatus::ContactLater->value,
            ])->count();

            $closedThisMonth = Referral::where('status', ReferralStatus::Closed->value)
                ->whereMonth('closed_at', $now->month)
                ->whereYear('closed_at', $now->year)
                ->count();

            $totalUsers = User::count();

            $totalReferrals = Referral::count();
            $conversionRate = $totalReferrals > 0 ? round((Referral::where('status', ReferralStatus::Closed->value)->count() / $totalReferrals) * 100, 1) : 0;
            $avgCommission = Referral::where('status', ReferralStatus::Closed->value)->count() > 0
                ? $totalCommissionsGenerated / Referral::where('status', ReferralStatus::Closed->value)->count()
                : 0;

            // Top Offerings
            $topOfferings = Offering::withCount('referrals')
                ->orderBy('referrals_count', 'desc')
                ->limit(5)
                ->get()
                ->map(fn ($o) => ['name' => $o->name, 'count' => $o->referrals_count]);

            // Top Associates (By closed referrals)
            $topAssociates = Associate::with(['user'])->withCount(['referrals' => function ($q) {
                $q->where('status', ReferralStatus::Closed->value);
            }])
                ->orderBy('referrals_count', 'desc')
                ->limit(5)
                ->get()
                ->map(fn ($a) => [
                    'name' => $a->user->name,
                    'count' => $a->referrals_count,
                    'total_commissions' => $a->commissions()->where('status', '!=', 'Void')->sum('amount'),
                ]);

            $stats = [
                'total_commissions' => (float) $totalCommissionsGenerated,
                'paid_commissions' => (float) $totalCommissionsPaid,
                'pending_commissions' => (float) $totalCommissionsPending,
                'active_referrals' => $pendingReferralsCount,
                'closed_this_month' => $closedThisMonth,
                'total_referrals' => $totalReferrals,
                'successful_referrals' => Referral::where('status', ReferralStatus::Closed->value)->count(),
                'total_users' => $totalUsers,
                'conversion_rate' => $conversionRate,
                'avg_commission' => (float) $avgCommission,
                'top_offerings' => $topOfferings,
                'top_associates' => $topAssociates,
                'is_admin' => true,
            ];
        } else {
            $associate = $user->associate;
            $referralsQuery = Referral::where('associate_id', $associate?->id);

            // Associate Stats
            $myTotalPaid = Commission::where('associate_id', $associate?->id)
                ->where('status', CommissionStatus::Paid->value)
                ->sum('amount');

            $myTotalPending = Commission::where('associate_id', $associate?->id)
                ->where('status', CommissionStatus::Pending->value)
                ->sum('amount');

            $successfulReferrals = $referralsQuery->clone()->where('status', ReferralStatus::Closed->value)->count();
            $totalMyReferrals = $referralsQuery->clone()->count();
            $myConversionRate = $totalMyReferrals > 0 ? round(($successfulReferrals / $totalMyReferrals) * 100, 1) : 0;

            $myActiveReferrals = $referralsQuery->clone()->whereIn('status', [
                ReferralStatus::Prospect->value,
                ReferralStatus::Contacted->value,
                ReferralStatus::InProcess->value,
                ReferralStatus::ContactLater->value,
            ])->count();

            $upcomingReminders = $referralsQuery->clone()
                ->where('status', ReferralStatus::ContactLater->value)
                ->whereDate('reminder_date', '>=', $now->toDateString())
                ->count();

            $stats = [
                'current_balance' => (float) ($user->balance ?? 0),
                'pending_to_collect' => (float) $myTotalPending,
                'total_paid' => (float) $myTotalPaid,
                'successful_referrals' => $successfulReferrals,
                'total_referrals' => $totalMyReferrals,
                'active_referrals' => $myActiveReferrals,
                'upcoming_reminders' => $upcomingReminders,
                'conversion_rate' => $myConversionRate,
                'is_admin' => false,
            ];
        }

        // 2. Earnings (Always Personal Balance)
        $totalEarnings = $user->balance ?? 0;

        // 3. Recent Activity (Last 5)
        $recentReferrals = $referralsQuery->with('offering')
            ->latest()
            ->take(5)
            ->get();

        // 4. Chart Data (Monthly Commissions Paid)
        $chartData = Commission::query()
            ->when(! ($user->isAdmin() || $user->isEmployee()), function ($q) use ($user) {
                return $q->where('associate_id', $user->associate?->id);
            })
            ->where('status', CommissionStatus::Paid->value)
            ->whereYear('paid_at', date('Y'))
            ->get(['paid_at', 'amount']);

        $monthlyRevenue = $chartData->groupBy(function ($item) {
            return $item->paid_at ? Carbon::parse($item->paid_at)->format('m') : '00';
        })->map(function ($group) {
            return $group->sum('amount');
        });

        // Ensure all months are present for chart
        $revenueSeries = [];
        for ($i = 1; $i <= 12; $i++) {
            $month = str_pad($i, 2, '0', STR_PAD_LEFT);
            $revenueSeries[] = $monthlyRevenue->get($month, 0);
        }

        return [
            'stats' => $stats,
            'recentReferrals' => $recentReferrals,
            'monthlyRevenue' => $revenueSeries,
            'accountManager' => $user->associate?->referrer?->user ? [
                'name' => $user->associate->referrer->user->name,
                'email' => $user->associate->referrer->user->email,
                'phone' => $user->associate->referrer->user->phone,
                'logo_url' => $user->associate->referrer->user->logo_url,
            ] : null,
        ];
    }
}
