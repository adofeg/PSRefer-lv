<?php

namespace App\Http\Controllers\Api;

use App\Models\Referral;
use App\Models\Commission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
  public function revenue(Request $request)
  {
    $user = Auth::user();
    $targetUserId = ($user->role === 'associate') ? $user->id : $request->query('user_id');

    // Revenue (Deal Value handled, but revenue_generated is what matters for company? Or deal_value?)
    // Node: SELECT SUM(revenue_generated) ... FROM referrals WHERE status = 'Cerrado'

    $revenueQuery = Referral::where('status', 'Cerrado');
    $commissionsQuery = Commission::where('status', 'paid');

    if ($targetUserId) {
      $revenueQuery->where('user_id', $targetUserId);
      $commissionsQuery->where('user_id', $targetUserId);
    }

    $totalRevenue = $revenueQuery->sum('revenue_generated');
    $totalDeals = $revenueQuery->count();
    $totalCommissions = $commissionsQuery->sum('amount');

    return response()->json([
      'total_revenue' => (float) $totalRevenue,
      'total_deals' => (int) $totalDeals,
      'total_commissions' => (float) $totalCommissions,
    ]);
  }
}
