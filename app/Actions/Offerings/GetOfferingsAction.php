<?php

namespace App\Actions\Offerings;

use App\Models\Offering;
use Illuminate\Pagination\LengthAwarePaginator;

class GetOfferingsAction
{
    public function execute(): LengthAwarePaginator
    {
        return Offering::where('is_active', true)
            ->latest()
            ->paginate(12);
    }
}
