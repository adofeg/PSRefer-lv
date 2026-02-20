<?php

namespace App\Actions\Settings;

use App\Data\Settings\UserSettingsData;
use App\Models\Associate;
use App\Models\User;
use App\Models\FileAsset;
use Illuminate\Support\Str;

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
            // Remove old logo if exists
            if ($user->logo) {
                // Only delete file if it's local (disk=public) and not a shared asset
                if ($user->logo->disk === 'public') {
                     \Illuminate\Support\Facades\Storage::disk('public')->delete($user->logo->path);
                }
                $user->logo->delete();
            }

            $file = $data->logo_file;
            $path = $file->store('logos', 'public');

            $user->logo()->create([
                'uuid' => (string) Str::uuid(),
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'purpose' => 'logo',
                'category' => 'profile',
                'uploaded_by' => auth()->id(),
            ]);
        }

        $profile = $user->profileable;

        if ($profile instanceof Associate) {
            $profileData = [
                'payment_info' => $data->payment_info ?? [],
            ];

            // W-9 Status Logic Removed (File-based only)

            if ($data->category) {
                if (auth()->user()->hasRole(['admin', 'psadmin'])) {
                    $profileData['category'] = $data->category;
                }
            }

            $profile->update($profileData);

            if ($data->w9_file) {
                 // Remove old W9 if exists
                if ($profile->w9) {
                    if ($profile->w9->disk === 'public') {
                         \Illuminate\Support\Facades\Storage::disk('public')->delete($profile->w9->path);
                    }
                    $profile->w9->delete();
                }

                $file = $data->w9_file;
                $path = $file->store('w9_forms', 'public');

                $profile->w9()->create([
                    'uuid' => (string) Str::uuid(),
                    'disk' => 'public',
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'purpose' => 'w9',
                    'category' => 'legal',
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        return $user->refresh();
    }
}
