<?php

namespace App\Actions\Associate;

use App\Enums\ReferralStatus;
use App\Models\Commission;
use App\Models\Referral;
use App\Models\User;

class GetDashboardStatsAction
{
    public function execute(User $user, ?int $year = null, ?int $month = null): array
    {
        $year = $year ?? now()->year;
        // If month is null, we show the whole year. If set, we show days of that month.

        $associate = $user->associate;

        if (! $associate) {
            return [
                'total_earned' => 0,
                'pending_earned' => 0,
                'lost_potential' => 0,
                'total_referrals' => 0,
                'conversion_rate' => 0,
                'recent_activity' => [],
                'chart_data' => ['labels' => [], 'values' => []],
            ];
        }

        // Base query for KPIs (ALL TIME) - KPIs should imply current state or all time?
        // Usually Dashboard KPIs are "All Time" or "Current Status".
        // The user asked for filtering THE CHART.
        // So I will keep KPIs as "All Time" / "Current Status" to not confuse "Pending Payment" with "Pending Payment in 2024".
        // Pending Payment is a current backlog state.

        $referrals = Referral::where('associate_id', $associate->id)->get();

        // ... (KPI Logic remains same context) ...
        // Re-fetching or filtering for Chart separately might be cleaner but let's reuse if possible.
        // Actually, for the chart we need a specific query to filter by date.

        // 1. Calculate KPIs (Global Context)
        $totalEarned = Commission::where('associate_id', $associate->id)
            ->where('status', \App\Enums\CommissionStatus::Paid->value)
            ->sum('amount');

        $pendingEarned = Commission::where('associate_id', $associate->id)
            ->where('status', \App\Enums\CommissionStatus::Pending->value)
            ->sum('amount');

        $lostCount = $referrals->where('status', ReferralStatus::Lost->value)->count();
        $totalCount = $referrals->count();
        $wonCount = $referrals->whereIn('status', [ReferralStatus::Closed->value, ReferralStatus::Paid->value])->count();
        $conversionRate = $totalCount > 0 ? round(($wonCount / $totalCount) * 100, 1) : 0;

        // 2. Recent Activity (Global)
        $recentActivity = $referrals->sortByDesc('created_at')->take(5)->values()->map(function ($r) {
            return [
                'id' => $r->id,
                'client_name' => $r->client_name,
                'offering_name' => $r->offering?->name ?? 'N/A',
                'status' => $r->status,
                'date' => $r->created_at->format('d/m/Y'),
                'amount' => $r->commissions->sum('amount'),
            ];
        });

        // 3. Chart Data (Filtered)
        $chartQuery = Referral::where('associate_id', $associate->id)
            ->whereYear('created_at', $year);

        if ($month) {
            // Monthly View: Days 1-30/31
            $chartQuery->whereMonth('created_at', $month);
            $filteredReferrals = $chartQuery->get();

            $daysInMonth = \Carbon\Carbon::create($year, $month)->daysInMonth;
            $labels = range(1, $daysInMonth);
            $values = [];

            foreach ($labels as $day) {
                $count = $filteredReferrals->filter(fn ($r) => $r->created_at->day == $day)->count();
                $values[] = $count;
            }
        } else {
            // Yearly View: Months Jan-Dec
            $filteredReferrals = $chartQuery->get();
            $labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $values = [];

            foreach (range(1, 12) as $m) {
                $count = $filteredReferrals->filter(fn ($r) => $r->created_at->month == $m)->count();
                $values[] = $count;
            }
        }

        return [
            'total_earned' => $totalEarned,
            'pending_earned' => $pendingEarned,
            'lost_referrals' => $lostCount,
            'total_referrals' => $totalCount,
            'conversion_rate' => $conversionRate,
            'recent_activity' => $recentActivity,
            'chart_data' => [
                'labels' => $labels,
                'values' => $values,
            ],
            'filters' => [
                'year' => $year,
                'month' => $month,
                'available_years' => $this->getAvailableYears($associate->id),
            ],
        ];
    }

    private function getAvailableYears($associateId): array
    {
        $years = Referral::where('associate_id', $associateId)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        if (empty($years)) {
            return [now()->year];
        }

        // Ensure strictly continuous? Or just distinct years?
        // User asked: "año que empece hasta el actual". Distinct is fine.
        return $years;
    }

    private function calculatePotential(Referral $referral): float
    {
        return $referral->commissions()->where('status', \App\Enums\CommissionStatus::Pending->value)->sum('amount');
    }
}
