<?php

namespace App\Actions\Settings;

use App\Data\Settings\SmtpSettingsData;
use App\Models\SystemSetting;

class UpdateSmtpSettingsAction
{
    public function execute(SmtpSettingsData $data): SystemSetting
    {
        return SystemSetting::updateOrCreate(
            ['key' => 'smtp_config'],
            ['value' => json_encode($data->toArray())]
        );
    }
}
