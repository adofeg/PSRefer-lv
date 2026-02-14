<?php

namespace App\Actions\Financial;

use App\Data\Offerings\OfferingData;

class CalculateCommissionAction
{
    public function execute(OfferingData $offering, float $transactionAmount): float
    {
        // Explicitly handle null commission rate
        // Simple percentage calculation
        return ($transactionAmount * $offering->base_commission) / 100;
    }
}
