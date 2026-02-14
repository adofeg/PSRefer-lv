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
            $user->update(['logo_url' => '/storage/'.$path]);
        }

        $profile = $user->profileable;

        if ($profile instanceof Associate) {
            $profileData = [
                'payment_info' => $data->payment_info ?? [],
            ];

            // W-9 Status Logic (Manual Toggle or Admin Override)
            if ($data->w9_status) {
                // If user is Admin/PSAdmin, allow any status change
                if (auth()->user()->hasRole(['admin', 'psadmin'])) {
                    $profileData['w9_status'] = $data->w9_status->value;
                }
                // If Associate, allow toggling "submitted" / "pending" freely
                else {
                    $profileData['w9_status'] = $data->w9_status->value;
                }
            }

            if ($data->category) {
                if (auth()->user()->hasRole(['admin', 'psadmin'])) {
                    $profileData['category'] = $data->category;
                }
            }

            $profile->update($profileData);
        }

        return $user->refresh();
    }
}
