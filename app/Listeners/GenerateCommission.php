<?php

namespace App\Listeners;

use App\Enums\ReferralStatus;
use App\Events\ReferralClosed;

class GenerateCommission
{
    public function __construct(
        protected \App\Services\CommissionService $commissionService
    ) {}

    public function handle(ReferralClosed $event): void
    {
        $referral = $event->referral;

        if ($referral->status !== ReferralStatus::Closed->value) {
            return;
        }

        // Use the Service to handle calculation logic (Overrides, Rules, Recurring, etc.)
        $this->commissionService->createAllCommissions($referral, $referral->offering);
    }
}
