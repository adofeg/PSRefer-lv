<?php

namespace App\Http\Controllers\Private\Associate;

use App\Actions\Marketing\GetMarketingCenterDataAction;
use Inertia\Inertia;

class MarketingCenterController extends AssociateController
{
    public function index(GetMarketingCenterDataAction $action)
    {
        return Inertia::render('Private/Associate/MarketingCenter/Index', $action->execute(auth()->user()));
    }
}