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
            'phone' => $data->phone,
            'preferred_currency' => $data->preferred_currency->value,
        ]);

        if ($data->logo_file) {
            $path = $data->logo_file->store('logos', 'public');
            $user->update(['logo_url' => '/storage/' . $path]);
        }

        $profile = $user->profileable;

        if ($profile instanceof Associate) {
            $profileData = [
                'payment_info' => $data->payment_info ?? [],
            ];

            // Security: Only Admins/PSAdmins can change the Category or W-9 Status directly
            if (auth()->user()->hasRole(['admin', 'psadmin'])) {
                $profileData['w9_status'] = $data->w9_status->value;
                $profileData['category'] = $data->category;
            }

            if ($data->w9_file) {
                // When an associate uploads a file, we automatically set status to 'submitted'
                // unless it was already verified.
                if (!auth()->user()->hasRole(['admin', 'psadmin']) && $profile->w9_status !== 'verified') {
                    $profileData['w9_status'] = 'submitted';
                }
                
                $path = $data->w9_file->store('w9_forms', 'local');
                $profileData['w9_file_url'] = $path;
            }

            $profile->update($profileData);
        }

        return $user->refresh();
    }
}
