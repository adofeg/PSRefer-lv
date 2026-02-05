<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Dashboard\GetDashboardStatsAction;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends AdminController
{
    public function index(GetDashboardStatsAction $action)
    {
        $data = $action->execute(Auth::user());

        return Inertia::render('Dashboard', $data);
    }
}
