<?php

namespace App\Services;

use App\Models\Offering;
use App\Models\Referral;

/**
 * @deprecated Use App\Services\CommissionService instead.
 */
class CommissionCalculator
{
  /**
   * Calculate the commission amount based on offering rules and deal value.
   */
  public function calculate(Referral $referral): float
  {
    $offering = $referral->offering;

    if (!$offering) {
      return 0.0;
    }

    $dealValue = $referral->deal_value ?? 0;

    // 1. Check for specific rules in commission_rules JSON
    // Example format: [{"min": 0, "max": 1000, "rate": 10}, {"min": 1001, "rate": 15}]
    $rules = $offering->commission_rules ?? [];

    if (!empty($rules)) {
      foreach ($rules as $rule) {
        $min = $rule['min'] ?? 0;
        $max = $rule['max'] ?? PHP_FLOAT_MAX;

        if ($dealValue >= $min && $dealValue <= $max) {
          if (isset($rule['rate'])) {
            return ($dealValue * $rule['rate']) / 100;
          }
          if (isset($rule['fixed'])) {
            return $rule['fixed'];
          }
        }
      }
    }

    // 2. Fallback to basic commission rate
    if ($offering->commission_rate > 0) {
      return ($dealValue * $offering->commission_rate) / 100;
    }

    // 3. Fallback to base commission (flat fee)
    return $offering->base_commission ?? 0.0;
  }
}
