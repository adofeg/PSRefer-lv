<?php

namespace App\Http\Controllers\Private\Associate;

use App\Actions\Commissions\GetCommissionSummaryAction;
use App\Http\Requests\Associate\CommissionRequest;
use Inertia\Inertia;

class CommissionController extends AssociateController
{
    public function index(CommissionRequest $request, GetCommissionSummaryAction $action)
    {
        return Inertia::render('Private/Associate/Commissions/Index', $action->execute($request->user()));
    }
}
