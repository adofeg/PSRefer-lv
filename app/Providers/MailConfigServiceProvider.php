<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        try {
            // Check if table exists to avoid errors during initial migration
            if (!Schema::hasTable('system_settings')) {
                return;
            }

            $smtpConfig = SystemSetting::where('key', 'smtp_config')->first();

            if ($smtpConfig && $smtpConfig->value) {
                $config = json_decode($smtpConfig->value, true);

                if ($config) {
                    Config::set('mail.default', 'smtp');
                    $mailConfig = [
                        'mail.mailers.smtp.transport' => 'smtp',
                        'mail.mailers.smtp.host' => $config['host'],
                        'mail.mailers.smtp.port' => $config['port'],
                        'mail.mailers.smtp.encryption' => $config['encryption'],
                        'mail.mailers.smtp.username' => $config['username'],
                        'mail.mailers.smtp.password' => $config['password'],
                        'mail.from.address' => $config['from_address'],
                        'mail.from.name' => $config['from_name'],
                    ];

                    Config::set($mailConfig);
                }
            }
        } catch (Exception $e) {
            // Silently fail if something goes wrong to not break the app
        }
    }
}
