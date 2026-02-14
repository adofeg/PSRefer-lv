<?php

namespace App\Actions\Commissions;

use App\Models\CommissionOverride;

class UpsertCommissionOverrideAction
{
    public function __construct(
        protected \App\Services\AuditService $auditService
    ) {}

    public function execute(int $associateId, ?int $offeringId, float $baseCommission): CommissionOverride
    {
        $override = CommissionOverride::updateOrCreate(
            ['associate_id' => $associateId, 'offering_id' => $offeringId],
            ['base_commission' => $baseCommission, 'is_active' => true]
        );

        $this->auditService->logAction(
            $override,
            $override->wasRecentlyCreated ? 'CREATE' : 'UPDATE',
            "Commission Override set to {$baseCommission}% for Associate #{$associateId}",
            null,
            ['rate' => $baseCommission]
        );

        return $override;
    }
}
