<?php

namespace App\Providers;

use App\Models\Offering;
use App\Models\Referral;
use App\Policies\Admin\OfferingPolicy;
use App\Policies\Admin\ReferralPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Offering::class => OfferingPolicy::class,
        Referral::class => ReferralPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
