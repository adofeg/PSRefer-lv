<?php

namespace App\Actions\Commissions;

use App\Models\CommissionOverride;

class DeleteCommissionOverrideAction
{
    public function __construct(
        protected \App\Services\AuditService $auditService
    ) {}

    public function execute(CommissionOverride $override): void
    {
        $this->auditService->logAction(
            $override,
            'DELETE',
            "Commission Override deleted for Associate #{$override->associate_id}"
        );

        $override->delete();
    }
}
