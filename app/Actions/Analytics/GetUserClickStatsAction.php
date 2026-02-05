<?php

namespace App\Actions\Analytics;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class GetUserClickStatsAction
{
    public function execute(User $user): array
    {
        $associateId = $user->associateProfile()?->id;

        $totalClicks = DB::table('referral_clicks')
            ->where('referrer_associate_id', $associateId)
            ->count();

        $conversions = DB::table('referral_clicks')
            ->where('referrer_associate_id', $associateId)
            ->where('link_type', 'conversion')
            ->count();

        $clicksByOffering = DB::table('referral_clicks')
            ->join('offerings', 'referral_clicks.offering_id', '=', 'offerings.id')
            ->where('referral_clicks.referrer_associate_id', $associateId)
            ->select('offerings.name', DB::raw('count(*) as count'))
            ->groupBy('offerings.name')
            ->orderBy('count', 'desc')
            ->get();

        $clicksLast7Days = DB::table('referral_clicks')
            ->where('referrer_associate_id', $associateId)
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
