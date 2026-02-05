<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Marketing\GetAdminMarketingDataAction;
use Inertia\Inertia;

class MarketingController extends AdminController
{
    public function index(GetAdminMarketingDataAction $action)
    {
        return Inertia::render('Marketing/Index', $action->execute(auth()->user()));
    }
}