<?php

namespace App\Actions\Offerings;

use App\Models\Offering;

class UpdateOfferingAction
{
    public function execute(Offering $offering, array $data): Offering
    {
        $offering->update($data);
        return $offering->refresh();
    }
}
