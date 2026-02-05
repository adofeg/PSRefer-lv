<?php

namespace App\Actions\Offerings;

use App\Models\Offering;
use App\Data\Offerings\OfferingData;

class CreateOfferingAction
{
    public function execute(array $data, int $ownerId): Offering
    {
        return Offering::create([
            ...$data,
            'owner_id' => $ownerId,
            'is_active' => true
        ]);
    }
}
