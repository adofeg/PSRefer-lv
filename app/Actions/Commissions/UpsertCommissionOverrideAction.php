<?php

namespace App\Actions\Commissions;

use App\Models\CommissionOverride;

class UpsertCommissionOverrideAction
{
    public function execute(int $associateId, int $offeringId, float $commissionRate): CommissionOverride
    {
        return CommissionOverride::updateOrCreate(
            ['associate_id' => $associateId, 'offering_id' => $offeringId],
            ['commission_rate' => $commissionRate, 'is_active' => true]
        );
    }
}
