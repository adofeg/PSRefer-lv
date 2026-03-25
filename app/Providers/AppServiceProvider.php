<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->bootPolicyDiscovery();
        $this->bootDynamicSmtpConfiguration();
    }

    protected function bootPolicyDiscovery(): void
    {
        \Illuminate\Support\Facades\Gate::guessPolicyNamesUsing(function ($modelClass) {
            $classBaseName = \Illuminate\Support\Str::afterLast($modelClass, '\\');

            // Path detection (safest for layers)
            $isAssociateArea = request()->is('associate/*') || request()->is('associate');

            if ($isAssociateArea) {
                $policyFile = app_path("Policies/Associate/{$classBaseName}Policy.php");
                if (file_exists($policyFile)) {
                    return "App\\Policies\\Associate\\{$classBaseName}Policy";
                }
            }

            // Fallback to Admin policy
            $adminPolicyFile = app_path("Policies/Admin/{$classBaseName}Policy.php");
            if (file_exists($adminPolicyFile)) {
                return "App\\Policies\\Admin\\{$classBaseName}Policy";
            }

            return null; // Laravel default
        });
    }

    protected function bootDynamicSmtpConfiguration(): void
    {
        try {
            // Allow disabling dynamic config via .env
            if (! env('MAIL_DYNAMIC_CONFIG', true)) {
                return;
            }

            // We only skip in unit tests to avoid hitting real SMTP
            if (app()->runningUnitTests()) {
                return;
            }

            $smtpConfig = \App\Models\SystemSetting::where('key', 'smtp_config')->first();
            
            if ($smtpConfig) {
                $config = json_decode($smtpConfig->value, true);
                
                if (!empty($config['host'])) {
                    config([
                        'mail.default' => 'smtp',
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
        } catch (\Exception $e) {
            // Log error but don't crash the app if DB is not ready (e.g. during migrations)
            if (! str_contains($e->getMessage(), 'Table') && ! str_contains($e->getMessage(), 'column')) {
                \Illuminate\Support\Facades\Log::warning("Could not load dynamic SMTP config: " . $e->getMessage());
            }
        }
    }
}
