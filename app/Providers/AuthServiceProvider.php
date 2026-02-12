<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Commission;
use App\Models\CommissionOverride;
use App\Models\Offering;
use App\Models\Referral;
use App\Models\User;
use App\Policies\Admin\CategoryPolicy;
use App\Policies\Admin\CommissionOverridePolicy;
use App\Policies\Admin\CommissionPolicy;
use App\Policies\Admin\OfferingPolicy;
use App\Policies\Admin\ReferralPolicy;
use App\Policies\Admin\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Category::class => CategoryPolicy::class,
        Commission::class => CommissionPolicy::class,
        CommissionOverride::class => CommissionOverridePolicy::class,
        Offering::class => OfferingPolicy::class,
        Referral::class => ReferralPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
