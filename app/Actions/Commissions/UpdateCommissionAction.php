<?php

namespace App\Actions\Commissions;

use App\Models\Commission;
use Illuminate\Support\Facades\DB;

class UpdateCommissionAction
{
    public function execute(Commission $commission, array $data): Commission
    {
        return DB::transaction(function () use ($commission, $data) {
            $commission->update([
                'associate_id' => $data['associate_id'],
                'referral_id' => $data['referral_id'] ?? null,
                'amount' => $data['amount'],
                'commission_type' => $data['commission_type'],
                'status' => $data['status'],
                'metadata' => array_merge($commission->metadata ?? [], ['notes' => $data['notes'] ?? '']),
            ]);

            return $commission->refresh();
        });
    }
}
