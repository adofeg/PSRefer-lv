<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Dashboard\GetDashboardStatsAction;
use App\Http\Requests\Admin\DashboardRequest;
use Inertia\Inertia;

class DashboardController extends AdminController
{
    public function index(DashboardRequest $request, GetDashboardStatsAction $action)
    {
        $data = $action->execute($request->user());

        return Inertia::render('Private/Admin/Dashboard/Index', $data);
    }
}
