<?php

namespace App\Actions\Commissions;

use App\Models\Commission;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class GetAllCommissionsAction
{
    public function execute(array $filters = []): LengthAwarePaginator
    {
        $query = Commission::query()
            ->with(['associate.user', 'referral', 'referral.offering']);

        // Apply Search Filter (Associate Name or Client Name)
        $query->when($filters['search'] ?? null, function (Builder $query, $search) {
            $query->whereHas('associate.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
                ->orWhereHas('referral', function ($q) use ($search) {
                    $q->where('client_name', 'like', "%{$search}%");
                });
        });

        // Apply Status Filter
        $query->when($filters['status'] ?? null, function (Builder $query, $status) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        });

        // Apply Associate Filter
        $query->when($filters['associate_id'] ?? null, function (Builder $query, $associateId) {
            $query->where('associate_id', $associateId);
        });

        return $query->latest()
            ->paginate(15)
            ->withQueryString();
    }
}
