<?php

namespace App\Actions\Financial;

use App\Data\Offerings\OfferingData;

class CalculateCommissionAction
{
    public function execute(OfferingData $offering, float $transactionAmount): float
    {
        // Explicitly handle null commission rate
        if ($offering->commission_rate === null) {
            return 0.0;
        }

        // Simple percentage calculation
        return ($transactionAmount * $offering->commission_rate) / 100;
    }
}
