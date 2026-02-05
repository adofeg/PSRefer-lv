<?php

namespace App\Actions\Commissions;

use App\Models\CommissionOverride;
use Illuminate\Support\Collection;

class GetCommissionOverridesAction
{
    public function execute(int $associateId): Collection
    {
        return CommissionOverride::where('associate_id', $associateId)
            ->with('offering:id,name,commission_rate')
            ->get();
    }
}
