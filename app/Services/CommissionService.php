<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Offering;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CommissionService
{
  /**
   * Calculate commissions based on offering config and override
   */
  public function calculateCommissions(Referral $referral, Offering $offering, $override = null)
  {
    $commissions = [];
    $dealValue = $referral->deal_value ?? $referral->revenue_generated ?? 0;

    // Offering Config
    $config = $offering->commission_config ?? [];

    // 1. Override (Highest Priority)
    if ($override && isset($override->commission_rate)) {
      $percentage = $override->commission_rate;
      $amount = $dealValue > 0 ? ($dealValue * $percentage) / 100 : 0;

      $commissions[] = [
        'type' => 'override',
        'amount' => round($amount, 2),
        'percentage' => $percentage,
        'recurrence_type' => 'one_time',
      ];
      return $commissions;
    }

    // 2. Fixed Amount
    if (isset($config['fixed_amount']) && $config['fixed_amount'] > 0) {
      $commissions[] = [
        'type' => 'fixed',
        'amount' => floatval($config['fixed_amount']),
        'percentage' => 0,
        'recurrence_type' => 'one_time',
      ];
    }

    // 3. Percentage
    if (isset($config['percentage']) && $config['percentage'] > 0 && $dealValue > 0) {
      $amount = ($dealValue * $config['percentage']) / 100;
      $commissions[] = [
        'type' => 'percentage',
        'amount' => round($amount, 2),
        'percentage' => $config['percentage'],
        'recurrence_type' => 'one_time',
      ];
    } elseif (!isset($config['percentage']) && $offering->commission_rate > 0 && $dealValue > 0) {
      // Legacy Fallback
      $amount = ($dealValue * $offering->commission_rate) / 100;
      $commissions[] = [
        'type' => 'percentage',
        'amount' => round($amount, 2),
        'percentage' => $offering->commission_rate,
        'recurrence_type' => 'one_time',
      ];
    }

    // 4. Monthly Recurring
    if (isset($config['monthly']) && ($config['monthly']['amount'] ?? 0) > 0) {
      $duration = $config['monthly']['duration_months'] ?? null;
      $endDate = $duration ? now()->addMonths($duration) : null;

      $commissions[] = [
        'type' => 'monthly',
        'amount' => floatval($config['monthly']['amount']),
        'percentage' => 0,
        'recurrence_type' => 'recurring',
        'recurrence_interval' => 'monthly',
        'recurrence_end_date' => $endDate,
      ];
    }

    return $commissions;
  }

  public function createAllCommissions(Referral $referral, Offering $offering, $override = null)
  {
    $configs = $this->calculateCommissions($referral, $offering, $override);
    $created = [];

    foreach ($configs as $config) {
      $commission = Commission::create([
        'referral_id' => $referral->id,
        'user_id' => $referral->user_id,
        'amount' => $config['amount'],
        'commission_percentage' => $config['percentage'],
        'commission_type' => $config['type'],
        'recurrence_type' => $config['recurrence_type'],
        'recurrence_interval' => $config['recurrence_interval'] ?? null,
        'recurrence_end_date' => $config['recurrence_end_date'] ?? null,
        'status' => 'pending',
      ]);
      $created[] = $commission;
    }

    return $created;
  }
}
