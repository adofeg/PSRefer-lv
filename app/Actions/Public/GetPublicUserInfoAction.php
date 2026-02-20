<?php

namespace App\Actions\Public;

use App\Models\User;

class GetPublicUserInfoAction
{
    public function execute(int $userId): array
    {
        $user = User::query()
            ->select(['id', 'name', 'email', 'phone'])
            ->findOrFail($userId);

        return [
            'id' => (int) $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'logo_url' => $user->logo_url,
        ];
    }
}
