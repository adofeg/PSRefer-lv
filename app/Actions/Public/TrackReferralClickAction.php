<?php

namespace App\Actions\Public;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrackReferralClickAction
{
    public function execute(int $referrerId, ?int $offeringId, Request $request, string $linkType = 'specific'): void
    {
        DB::table('referral_clicks')->insert([
            'referrer_associate_id' => $referrerId,
            'offering_id' => $offeringId,
            'link_type' => $linkType,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'clicked_at' => now(),
        ]);
    }
}
