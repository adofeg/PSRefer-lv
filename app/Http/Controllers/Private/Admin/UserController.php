<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Auth\CreateUserAction;
use App\Actions\Users\GetUsersAction;
use App\Actions\Users\ToggleUserStatusAction;
use App\Enums\RoleName;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends AdminController
{
    public function __construct(
        protected AuditService $auditService
    )
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(UserRequest $request, GetUsersAction $action)
    {
        $filters = $request->only(['search', 'role', 'status']);

        return Inertia::render('Private/Admin/Users/Index', [
            'users' => $action->execute($filters),
            'filters' => $filters,
            'roles' => RoleName::cases(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Private/Admin/Users/Create', [
            'roles' => RoleName::cases(),
        ]);
    }

    public function store(UserRequest $request, CreateUserAction $action)
    {
        $action->execute($request->toData());

        return to_route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $user->load('roles');
        // Ensure profile is loaded for form binding
        $user->associateProfile();
        $user->employeeProfile();

        return Inertia::render('Private/Admin/Users/Edit', [
            'user' => $user,
            'roles' => RoleName::cases(),
        ]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        
        $oldData = $user->only(['name', 'email', 'is_active']);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? $user->is_active,
        ]);

        $this->auditService->logAction(
            $user, 
            'UPDATE', 
            "User '{$user->name}' updated by Admin",
            $oldData,
            $user->only(['name', 'email', 'is_active'])
        );

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }
        
        // Sync Roles
        $user->syncRoles([$data['role']]);

        // Update Phone/Category on Profile
        if ($user->profileable) {
            $profileData = [];
            if (isset($data['phone'])) $profileData['phone'] = $data['phone'];
            if (isset($data['category']) && $user->hasRole(RoleName::Associate->value)) {
                $profileData['category'] = $data['category'];
            }
            
            if (!empty($profileData)) {
                $user->profileable->update($profileData);
            }
        }

        return to_route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete yourself.');
        }

        $this->auditService->logAction(
            $user,
            'DELETE',
            "User '{$user->name}' deleted by Admin"
        );

        $user->delete(); // Soft Delete

        return back()->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(UserRequest $request, User $user, ToggleUserStatusAction $action)
    {
        $this->authorize('update', $user);

        $isActive = (bool) $request->boolean('is_active');
        $action->execute($user, $isActive);

        return back()->with('success', $isActive ? 'User activated.' : 'User deactivated.');
    }
}
