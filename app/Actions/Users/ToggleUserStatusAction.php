<?php

namespace App\Actions\Users;

use App\Models\User;

class ToggleUserStatusAction
{
    public function __construct(
        protected \App\Services\AuditService $auditService
    ) {}

    public function execute(User $user, bool $isActive): User
    {
        $oldStatus = $user->is_active;

        $user->update([
            'is_active' => $isActive,
        ]);

        $this->auditService->logAction(
            $user,
            'UPDATE',
            "User '{$user->name}' status changed to ".($isActive ? 'Active' : 'Inactive'),
            ['is_active' => $oldStatus],
            ['is_active' => $isActive]
        );

        return $user->refresh();
    }
}
