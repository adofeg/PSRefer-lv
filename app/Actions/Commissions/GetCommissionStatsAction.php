<?php

namespace App\Actions\Commissions;

use App\Models\Commission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GetCommissionStatsAction
{
    public function execute(): array
    {
        // 1. Totals
        $totals = Commission::selectRaw("
            SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_paid,
            SUM(CASE WHEN status = 'pending' THEN amount ELSE 0 END) as total_pending,
            SUM(CASE WHEN status = 'void' THEN amount ELSE 0 END) as total_void,
            COUNT(CASE WHEN status = 'paid' THEN 1 END) as count_paid,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as count_pending
        ")->first();

        // 2. Monthly Trend (Last 12 Months)
        $monthlyStats = Commission::selectRaw("
            DATE_FORMAT(created_at, '%Y-%m') as month,
            SUM(amount) as total
        ")
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->where('status', '!=', 'void')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->month => $item->total]);

        // Fill missing months
        $chartData = [];
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('Y-m');
            $labels[] = Carbon::now()->subMonths($i)->format('M Y');
            $chartData[] = $monthlyStats[$month] ?? 0;
        }

        // 3. Top Associates
        $topAssociates = Commission::select('associate_id', DB::raw('SUM(amount) as total_earned'))
            ->where('status', 'paid')
            ->with('associate.user')
            ->groupBy('associate_id')
            ->orderByDesc('total_earned')
            ->limit(5)
            ->get()
            ->map(fn ($c) => [
                'name' => $c->associate->user->name ?? 'Unknown',
                'amount' => $c->total_earned,
            ]);

        return [
            'totals' => [
                'paid' => $totals->total_paid ?? 0,
                'pending' => $totals->total_pending ?? 0,
                'void' => $totals->total_void ?? 0,
                'count_paid' => $totals->count_paid ?? 0,
                'count_pending' => $totals->count_pending ?? 0,
            ],
            'chart' => [
                'labels' => $labels,
                'data' => $chartData,
            ],
            'top_associates' => $topAssociates,
        ];
    }
}
