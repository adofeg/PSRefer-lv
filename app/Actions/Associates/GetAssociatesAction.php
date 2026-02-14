<?php

namespace App\Actions\Associates;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Support\Collection;

class GetAssociatesAction
{
    public function execute(): Collection
    {
        return User::role(RoleName::Associate->value)
            ->where('is_active', true)
            ->where('profileable_type', \App\Models\Associate::class)
            ->with('profileable')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->profileable?->id,
                    'name' => $user->name.' ('.$user->email.')',
                ];
            })
            ->filter(fn ($item) => $item['id'] !== null)
            ->values();
    }
}
