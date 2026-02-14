<?php

namespace App\Http\Controllers\Private;

use App\Actions\Dashboard\DetermineDashboardRedirectAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Private\DashboardRedirectRequest;

class DashboardRedirectController extends Controller
{
    public function __invoke(DashboardRedirectRequest $request, DetermineDashboardRedirectAction $action)
    {
        return redirect()->route(
            $action->execute($request->user())
        );
    }
}
