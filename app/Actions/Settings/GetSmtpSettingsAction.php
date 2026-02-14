<?php

namespace App\Actions\Settings;

use App\Data\Settings\SmtpSettingsData;
use App\Models\SystemSetting;

class GetSmtpSettingsAction
{
    public function execute(): SmtpSettingsData
    {
        $smtpConfig = SystemSetting::where('key', 'smtp_config')->first();
        $config = $smtpConfig ? json_decode($smtpConfig->value, true) : [
            'host' => '',
            'port' => '587',
            'username' => '',
            'password' => '',
            'encryption' => 'tls',
            'from_address' => 'noreply@example.com',
            'from_name' => 'PS Refer',
        ];

        return new SmtpSettingsData(
            host: $config['host'],
            port: $config['port'],
            username: $config['username'],
            password: $config['password'],
            encryption: $config['encryption'],
            from_address: $config['from_address'],
            from_name: $config['from_name']
        );
    }
}
