<?php

namespace App\Actions\Associate;

use App\Enums\ReferralStatus;
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
        $totalEarned = $referrals->where('status', ReferralStatus::Paid->value)->sum('revenue_generated');

        $pendingReferrals = $referrals->filter(function ($r) {
            return in_array($r->status, [ReferralStatus::Closed->value, ReferralStatus::InProcess->value])
                   && $r->status !== ReferralStatus::Paid->value;
        });
        $pendingEarned = $pendingReferrals->sum(fn ($r) => $this->calculatePotential($r));

        $lostReferrals = $referrals->where('status', ReferralStatus::Lost->value);
        $lostPotential = $lostReferrals->sum(fn ($r) => $this->calculatePotential($r));

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
                'amount' => $r->revenue_generated > 0 ? $r->revenue_generated : $this->calculatePotential($r),
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
                // Count referrals for this day
                $count = $filteredReferrals->filter(function ($r) use ($day) {
                    return $r->created_at->day == $day;
                })->count();
                $values[] = $count;
            }
        } else {
            // Yearly View: Months Jan-Dec
            $filteredReferrals = $chartQuery->get();
            $labels = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $values = [];

            foreach (range(1, 12) as $m) {
                $count = $filteredReferrals->filter(function ($r) use ($m) {
                    return $r->created_at->month == $m;
                })->count();
                $values[] = $count;
            }
        }

        return [
            'total_earned' => $totalEarned,
            'pending_earned' => $pendingEarned,
            'lost_potential' => $lostPotential,
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
        // If already has revenue generated (e.g. partial payment or closed), use it
        if ($referral->revenue_generated > 0) {
            return $referral->revenue_generated;
        }

        $offering = $referral->offering;
        if (! $offering) {
            return 0.0;
        }

        $config = $offering->commission_config ?? [];

        // Fixed Amount
        if (isset($config['fixed_amount']) && $config['fixed_amount'] > 0) {
            return (float) $config['fixed_amount'];
        }

        // Percentage
        // We need a deal deal_value to calc percentage.
        // If lost, we might not have it. If In Process, we might.
        if (isset($config['percentage']) && $config['percentage'] > 0) {
            $value = $referral->deal_value ?? 0; // If deal_value is null, potential is 0

            return ($value * $config['percentage']) / 100;
        }

        // Fallback to legacy fields if config missing
        if ($offering->base_commission > 0) {
            return $offering->base_commission;
        }

        return 0.0;
    }
}
