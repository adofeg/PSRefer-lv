<?php

namespace App\Actions\Marketing;

use App\Models\Offering;
use App\Models\User;

class GetAdminMarketingDataAction
{
    public function execute(User $user): array
    {
        return [
            'offerings' => Offering::where('is_active', true)->get(),
            'user' => $user,
        ];
    }
}
