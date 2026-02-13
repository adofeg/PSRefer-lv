<?php

namespace App\Http\Controllers\Private\Associate;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(\App\Actions\Associate\GetDashboardStatsAction $action): Response
    {
        return Inertia::render('Private/Associate/Dashboard', $action->execute(
            request()->user(),
            request()->input('year'),
            request()->input('month')
        ));
    }
}
