<?php

namespace App\Actions\Analytics;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetUserClickStatsAction
{
    public function execute(User $user): array
    {
        $isAdmin = $user->hasRole(\App\Enums\RoleName::adminRoles());
        $associateId = $user->associateProfile()?->id;

        $query = DB::table('referral_clicks');

        if (! $isAdmin) {
            $query->where('referrer_associate_id', $associateId);
        }

        $totalClicks = (clone $query)->count();

        $conversions = (clone $query)->where('link_type', 'conversion')->count();

        $clicksByOffering = (clone $query)
            ->join('offerings', 'referral_clicks.offering_id', '=', 'offerings.id')
            ->select('offerings.name', DB::raw('count(*) as count'))
            ->groupBy('offerings.name')
            ->orderBy('count', 'desc')
            ->get();

        $clicksLast7Days = (clone $query)
            ->where('clicked_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(clicked_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'total_clicks' => $totalClicks,
            'conversions' => $conversions,
            'conversion_rate' => $totalClicks > 0 ? round(($conversions / $totalClicks) * 100, 2) : 0,
            'by_offering' => $clicksByOffering,
            'last_7_days' => $clicksLast7Days,
        ];
    }
}
