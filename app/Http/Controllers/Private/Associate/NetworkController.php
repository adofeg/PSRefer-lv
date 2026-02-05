<?php

namespace App\Http\Controllers\Private\Associate;

use App\Actions\Network\GetNetworkOverviewAction;
use App\Enums\RoleName;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NetworkController extends AssociateController
{
    public function index(GetNetworkOverviewAction $action)
    {
        $user = Auth::user();
        $data = $action->execute($user);

        return Inertia::render('Network/Index', [
            'isAdmin' => $user->hasRole(RoleName::adminRoles()),
            ...$data,
        ]);
    }
}