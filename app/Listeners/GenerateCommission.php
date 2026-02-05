<?php

namespace App\Listeners;

use App\Events\ReferralClosed;
use App\Models\Commission;
use App\Services\CommissionCalculator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class GenerateCommission
{
  public function __construct(
    protected CommissionCalculator $calculator
  ) {}

  public function handle(ReferralClosed $event): void
  {
    $referral = $event->referral;

    if ($referral->status !== 'Cerrado') {
      return;
    }

    $amount = $this->calculator->calculate($referral);

    if ($amount > 0) {
      Commission::create([
        'id' => Str::uuid(),
        'referral_id' => $referral->id,
        'user_id' => $referral->user_id,
        'amount' => $amount,
        'commission_percentage' => $referral->offering->commission_rate,
        'status' => 'pending',
        'commission_type' => 'direct',
      ]);
    }
  }
}
