<?php

namespace App\Listeners;

use App\Events\ReferralClosed;
use App\Enums\ReferralStatus;
use App\Models\Commission;
use App\Services\CommissionCalculator;
use App\Enums\CommissionStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateCommission
{
  public function __construct(
    protected CommissionCalculator $calculator
  ) {}

  public function handle(ReferralClosed $event): void
  {
    $referral = $event->referral;

    if ($referral->status !== ReferralStatus::Closed->value) {
      return;
    }

    $amount = $this->calculator->calculate($referral);

    if ($amount > 0) {
      Commission::create([
        'referral_id' => $referral->id,
        'associate_id' => $referral->associate_id,
        'amount' => $amount,
        'commission_percentage' => $referral->offering->commission_rate,
        'status' => CommissionStatus::Pending->value,
        'commission_type' => 'direct',
      ]);
    }
  }
}
