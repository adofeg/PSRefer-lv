<?php

namespace App\Actions\Commissions;

use App\Models\CommissionOverride;

class DeleteCommissionOverrideAction
{
    public function execute(CommissionOverride $override): void
    {
        $override->delete();
    }
}
