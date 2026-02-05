<?php

namespace App\Http\Controllers\Private\Associate;

use App\Actions\Commissions\GetCommissionSummaryAction;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CommissionController extends AssociateController
{
    public function index(GetCommissionSummaryAction $action)
    {
        return Inertia::render('Commissions/Index', $action->execute(Auth::user()));
    }
}