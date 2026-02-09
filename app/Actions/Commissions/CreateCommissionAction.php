<?php

namespace App\Actions\Commissions;

use App\Models\Commission;
use Illuminate\Support\Facades\DB;

class CreateCommissionAction
{
    public function execute(array $data): Commission
    {
        return DB::transaction(function () use ($data) {
            return Commission::create([
                'associate_id' => $data['associate_id'],
                'referral_id' => $data['referral_id'] ?? null,
                'amount' => $data['amount'],
                'commission_type' => $data['commission_type'] ?? 'manual',
                'status' => $data['status'] ?? 'pending',
                'commission_percentage' => $data['commission_percentage'] ?? null,
                'recurrence_type' => 'one_time', // Default for manual
                'metadata' => $data['metadata'] ?? null,
            ]);
        });
    }
}
