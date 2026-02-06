<?php

namespace App\Http\Controllers\Api;

use App\Actions\Analytics\GetRevenueStatsAction;
use App\Actions\Analytics\GetUserClickStatsAction;
use App\Http\Requests\Api\ClickStatsRequest;
use App\Http\Requests\Api\RevenueStatsRequest;

class AnalyticsController extends ApiController
{
  public function clicks(ClickStatsRequest $request, GetUserClickStatsAction $action)
  {
    return response()->json(
      $action->execute($request->user())
    );
  }

  public function revenue(RevenueStatsRequest $request, GetRevenueStatsAction $action)
  {
    return response()->json(
      $action->execute($request->user(), $request->toData()->associate_id)
    );
  }
}
