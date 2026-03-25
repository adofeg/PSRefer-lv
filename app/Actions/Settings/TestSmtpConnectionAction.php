<?php

namespace App\Actions\Settings;

use App\Data\Settings\SmtpSettingsData;

class TestSmtpConnectionAction
{
    public function execute(SmtpSettingsData $data): array
    {
        try {
            // 1. First do a quick socket check
            $host = $data->host;
            $port = (int) $data->port;
            $timeout = 5;

            if ($data->encryption === 'ssl') {
                $host = 'ssl://'.$host;
            }

            $errno = 0;
            $errstr = '';
            $connection = @fsockopen($host, $port, $errno, $errstr, $timeout);

            if (! $connection) {
                return [
                    'success' => false,
                    'error' => "Connection failed: $errstr",
                ];
            }
            fclose($connection);

            // 2. Perform real authentication test by sending a test email
            // We use a temporary mailer configuration
            $config = [
                'transport' => 'smtp',
                'host' => $data->host,
                'port' => $data->port,
                'encryption' => $data->encryption,
                'username' => $data->username,
                'password' => $data->password,
                'timeout' => null,
                'local_domain' => env('MAIL_EHLO_DOMAIN'),
            ];

            config(['mail.mailers.smtp_test' => $config]);
            
            $user = auth()->user();
            $toEmail = $user ? $user->email : $data->from_address;

            \Illuminate\Support\Facades\Mail::mailer('smtp_test')->raw(
                "This is a test email to verify PS Refer SMTP configuration.\n\nSettings:\nHost: {$data->host}\nPort: {$data->port}\nUser: {$data->username}",
                function ($message) use ($data, $toEmail) {
                    $message->to($toEmail)
                        ->from($data->from_address, $data->from_name)
                        ->subject('SMTP Verification Test');
                }
            );

            return [
                'success' => true,
                'message' => 'SMTP connection and authentication successful. Test email sent to ' . $toEmail,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Authentication failed: ' . $e->getMessage(),
            ];
        }
    }
}
