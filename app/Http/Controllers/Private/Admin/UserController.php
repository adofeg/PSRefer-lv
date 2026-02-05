<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Auth\CreateUserAction;
use App\Actions\Users\ToggleUserStatusAction;
use App\Actions\Users\GetUsersAction;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Models\User;
use Inertia\Inertia;

class UserController extends AdminController
{
    public function index(GetUsersAction $action)
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => $action->execute()
        ]);
    }

    public function store(UserStoreRequest $request, CreateUserAction $action)
    {
        $action->execute($request->toData());

        return back()->with('success', 'User created successfully.');
    }

    public function toggleStatus(User $user, ToggleUserStatusAction $action)
    {
        $isActive = (bool) request()->boolean('is_active');
        $action->execute($user, $isActive);

        return response()->json([
            'message' => $isActive ? 'User activated successfully' : 'User deactivated successfully'
        ]);
    }
}