<?php

namespace App\Http\Controllers\Private\Associate;

use App\Actions\Associate\GetDashboardStatsAction;
use App\Http\Requests\Associate\DashboardRequest;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends AssociateController
{
    public function index(DashboardRequest $request, GetDashboardStatsAction $action): Response
    {
        return Inertia::render('Private/Associate/Dashboard', $action->execute($request->user()));
    }
}
