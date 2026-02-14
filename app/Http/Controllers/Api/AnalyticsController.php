<?php

namespace App\Http\Controllers\Api;

use App\Actions\Analytics\GetRevenueStatsAction;
use App\Actions\Analytics\GetUserClickStatsAction;
use App\Http\Requests\Api\AnalyticsRequest;

class AnalyticsController extends ApiController
{
    public function clicks(AnalyticsRequest $request, GetUserClickStatsAction $action)
    {
        return response()->json(
            $action->execute($request->user())
        );
    }

    public function revenue(AnalyticsRequest $request, GetRevenueStatsAction $action)
    {
        return response()->json(
            $action->execute($request->user(), $request->toRevenueData()->associate_id)
        );
    }
}
