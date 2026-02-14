<?php

namespace App\Actions\Commissions;

use App\Models\CommissionOverride;

class UpsertCommissionOverrideAction
{
    public function __construct(
        protected \App\Services\AuditService $auditService
    ) {}

    public function execute(int $associateId, ?int $offeringId, float $commissionRate): CommissionOverride
    {
        $override = CommissionOverride::updateOrCreate(
            ['associate_id' => $associateId, 'offering_id' => $offeringId],
            ['commission_rate' => $commissionRate, 'is_active' => true]
        );

        $this->auditService->logAction(
            $override,
            $override->wasRecentlyCreated ? 'CREATE' : 'UPDATE',
            "Commission Override set to {$commissionRate}% for Associate #{$associateId}",
            null,
            ['rate' => $commissionRate]
        );

        return $override;
    }
}
