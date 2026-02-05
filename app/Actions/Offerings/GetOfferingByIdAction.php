<?php

namespace App\Actions\Offerings;

use App\Models\Offering;

class GetOfferingByIdAction
{
    public function execute(int $id): Offering
    {
        return Offering::with('category:id,name')->findOrFail($id);
    }
}