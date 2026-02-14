<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Marketing\GetAdminMarketingDataAction;
use App\Http\Requests\Admin\MarketingRequest;
use Inertia\Inertia;

class MarketingController extends AdminController
{
    public function index(MarketingRequest $request, GetAdminMarketingDataAction $action)
    {
        return Inertia::render('Private/Admin/Marketing/Index', $action->execute($request->user()));
    }
}
