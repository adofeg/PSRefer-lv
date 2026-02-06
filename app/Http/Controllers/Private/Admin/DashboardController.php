<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Dashboard\GetDashboardStatsAction;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends AdminController
{
    public function index(GetDashboardStatsAction $action)
    {
        $data = $action->execute(Auth::user());

        return Inertia::render('Private/Admin/Dashboard/Index', $data);
    }
}