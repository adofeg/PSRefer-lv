<?php

namespace App\Actions\Settings;

use App\Data\Settings\UserSettingsData;
use App\Models\Associate;
use App\Models\User;

class UpdateUserSettingsAction
{
    public function execute(User $user, UserSettingsData $data): User
    {
        $user->update([
            'name' => $data->name,
        ]);

        $profile = $user->profileable;

        if ($profile instanceof Associate) {
            $profileData = [
                'w9_status' => $data->w9_status->value,
                'payment_info' => $data->payment_info ?? [],
                'category' => $data->category,
                'phone' => $data->phone,
            ];

            if ($data->logo_file) {
                $path = $data->logo_file->store('logos', 'public');
                $profileData['logo_url'] = '/storage/' . $path;
            }

            if ($data->w9_file) {
                $path = $data->w9_file->store('w9_forms', 'private');
                $profileData['w9_file_url'] = '/storage/' . $path;
            }

            $profile->update($profileData);
        }

        return $user->refresh();
    }
}
