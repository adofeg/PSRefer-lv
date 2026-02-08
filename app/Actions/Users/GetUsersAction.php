<?php

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GetUsersAction
{
    public function execute(array $filters = []): LengthAwarePaginator
    {
        return User::query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['role'] ?? null, function ($query, $role) {
                $query->role($role);
            })
            ->when(isset($filters['status']), function ($query) use ($filters) {
                if ($filters['status'] !== 'all') {
                    $query->where('is_active', filter_var($filters['status'], FILTER_VALIDATE_BOOLEAN));
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();
    }
}
