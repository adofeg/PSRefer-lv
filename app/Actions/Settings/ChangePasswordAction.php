<?php

namespace App\Actions\Settings;

use App\Data\Settings\PasswordChangeData;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ChangePasswordAction
{
    public function execute(User $user, PasswordChangeData $data): User
    {
        $user->update([
            'password' => Hash::make($data->new_password),
        ]);

        return $user->refresh();
    }
}
