<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Exception;
use Illuminate\Support\ServiceProvider;

class MailConfigServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function boot(): void
    {
        try {
            if (!app()->runningInConsole() || app()->runningUnitTests()) {
                $smtpConfig = SystemSetting::where('key', 'smtp_config')->first();
                
                if ($smtpConfig) {
                    $config = json_decode($smtpConfig->value, true);
                    
                    if ($config) {
                        config([
                            'mail.mailers.smtp.host' => $config['host'],
                            'mail.mailers.smtp.port' => $config['port'],
                            'mail.mailers.smtp.encryption' => $config['encryption'],
                            'mail.mailers.smtp.username' => $config['username'],
                            'mail.mailers.smtp.password' => $config['password'],
                            'mail.from.address' => $config['from_address'],
                            'mail.from.name' => $config['from_name'],
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            // Silently fail if table or key doesn't exist
        }
    }
}
