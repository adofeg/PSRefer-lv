<?php

namespace App\Actions\Settings;

use App\Data\Settings\SmtpSettingsData;

class TestSmtpConnectionAction
{
    public function execute(SmtpSettingsData $data): array
    {
        $host = $data->host;
        $port = (int) $data->port;
        $timeout = 5;

        if ($data->encryption === 'ssl') {
            $host = 'ssl://' . $host;
        }

        $errno = 0;
        $errstr = '';
        $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);

        if ($connection) {
            fclose($connection);

            return [
                'success' => true,
                'message' => 'SMTP connection successful.',
            ];
        }

        return [
            'success' => false,
            'error' => $errstr ?: 'SMTP connection failed.',
        ];
    }
}