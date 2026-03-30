<?php

namespace App\Actions\Commissions;

use App\Models\Commission;
use Illuminate\Database\Eloquent\Builder;

class GetAllCommissionsAction
{
    public function execute(array $filters = []): array
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
                    $q->searchByClient($search);
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

        // Apply Date Range Filter
        $query->when($filters['date_from'] ?? null, function (Builder $query, $from) {
            $query->whereDate('created_at', '>=', $from);
        });

        $query->when($filters['date_to'] ?? null, function (Builder $query, $to) {
            $query->whereDate('created_at', '<=', $to);
        });

        $allCommissions = $query->clone()->get();
        $totalAmount = $allCommissions->sum('amount');
        $paidAmount = $allCommissions->where('status', 'paid')->sum('amount');
        $pendingAmount = $allCommissions->where('status', 'pending')->sum('amount');

        return [
            'data' => $query->latest()->paginate(15)->withQueryString(),
            'total_amount_formatted' => '$'.number_format($totalAmount, 2),
            'paid_amount_formatted' => '$'.number_format($paidAmount, 2),
            'pending_amount_formatted' => '$'.number_format($pendingAmount, 2),
        ];
    }
}
