<?php

namespace App\Http\Controllers\Private;

use App\Actions\Dashboard\DetermineDashboardRedirectAction;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{
    public function __invoke(DetermineDashboardRedirectAction $action)
    {
        return redirect()->route(
            $action->execute(Auth::user())
        );
    }
}