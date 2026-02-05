<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Users\GetUsersAction;
use Inertia\Inertia;

class UserController extends AdminController
{
    public function index(GetUsersAction $action)
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => $action->execute()
        ]);
    }
}