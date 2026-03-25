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
}
