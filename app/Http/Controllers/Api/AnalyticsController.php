<?php

namespace App\Http\Controllers\Api;

use App\Actions\Analytics\GetRevenueStatsAction;
use App\Actions\Analytics\GetUserClickStatsAction;
use App\Http\Requests\Api\RevenueStatsRequest;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends ApiController
{
  public function clicks(GetUserClickStatsAction $action)
  {
    return response()->json(
      $action->execute(Auth::user())
    );
  }

  public function revenue(RevenueStatsRequest $request, GetRevenueStatsAction $action)
  {
    return response()->json(
      $action->execute(Auth::user(), $request->toData()->associate_id)
    );
  }
}
