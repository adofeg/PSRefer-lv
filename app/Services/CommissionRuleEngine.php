<?php

namespace App\Services;

use App\Models\Offering;
use App\Models\Referral;
use Illuminate\Support\Facades\Log;

class CommissionRuleEngine
{
    /**
     * Calculate commission based on offering rules or base rate.
     */
    public function calculateCommission(Referral $referral): float
    {
        $offering = $referral->offering;

        if (! $offering || ! $referral->deal_value) {
            return 0.0;
        }

        // If no rules defined, use base commission_rate
        if (empty($offering->commission_rules)) {
            return $this->calculateBaseCommission($referral->deal_value, $offering->commission_rate);
        }

        // Evaluate rules in order
        foreach ($offering->commission_rules as $rule) {
            // Check role eligibility (Parity with JS)
            if (isset($rule['roles']) && ! empty($rule['roles'])) {
                $allowedRoles = is_array($rule['roles']) ? $rule['roles'] : explode(',', $rule['roles']);
                $allowedRoles = array_map('trim', $allowedRoles);

                if ($referral->associate?->user && ! $referral->associate->user->hasRole($allowedRoles)) {
                    continue; // Skip rule if user role doesn't match
                }
            }

            if ($this->evaluateCondition($rule['condition'] ?? 'default', $referral)) {
                $rate = $rule['commission_rate'] ?? $offering->commission_rate;

                return $this->calculateBaseCommission($referral->deal_value, $rate);
            }
        }

        // Fallback to base rate
        return $this->calculateBaseCommission($referral->deal_value, $offering->commission_rate);
    }

    /**
     * Calculate base commission from value and rate.
     */
    private function calculateBaseCommission(float $dealValue, float $rate): float
    {
        return round($dealValue * ($rate / 100), 2);
    }

    /**
     * Evaluate a condition against referral data.
     */
    private function evaluateCondition(string $condition, Referral $referral): bool
    {
        // Default condition always matches
        if ($condition === 'default' || empty($condition)) {
            return true;
        }

        // Parse condition: "field operator value"
        // Examples: "deal_value >= 10000", "deal_value < 5000"
        $pattern = '/^(\w+)\s*(>=|<=|>|<|==|!=)\s*(\d+(?:\.\d+)?)$/';

        if (! preg_match($pattern, trim($condition), $matches)) {
            Log::warning("Invalid commission rule condition: {$condition}");

            return false;
        }

        [, $field, $operator, $value] = $matches;

        // Get field value from referral
        $fieldValue = $this->getFieldValue($referral, $field);

        if ($fieldValue === null) {
            return false;
        }

        // Convert to float for comparison
        $fieldValue = (float) $fieldValue;
        $value = (float) $value;

        // Evaluate operator
        return match ($operator) {
            '>=' => $fieldValue >= $value,
            '<=' => $fieldValue <= $value,
            '>' => $fieldValue > $value,
            '<' => $fieldValue < $value,
            '==' => abs($fieldValue - $value) < 0.01, // Float comparison with epsilon
            '!=' => abs($fieldValue - $value) >= 0.01,
            default => false
        };
    }

    /**
     * Get field value from referral.
     *
     * @return mixed
     */
    private function getFieldValue(Referral $referral, string $field)
    {
        // Direct referral properties
        if (property_exists($referral, $field)) {
            return $referral->$field;
        }

        // Check metadata
        if (isset($referral->metadata[$field])) {
            return $referral->metadata[$field];
        }

        return null;
    }

    /**
     * Preview commission rate for a given deal value.
     * Useful for showing users what commission they'll get.
     */
    public function previewCommissionRate(Offering $offering, float $dealValue): array
    {
        // Create temporary referral for evaluation
        $tempReferral = new Referral([
            'deal_value' => $dealValue,
        ]);
        $tempReferral->offering = $offering;

        // If no rules, return base rate
        if (empty($offering->commission_rules)) {
            return [
                'rate' => $offering->commission_rate,
                'label' => 'Base Rate',
                'commission' => $this->calculateBaseCommission($dealValue, $offering->commission_rate),
                'rule_matched' => null,
            ];
        }

        // Find matching rule
        foreach ($offering->commission_rules as $index => $rule) {
            // Check role eligibility (optional in preview if user context is available)
            if (isset($rule['roles']) && ! empty($rule['roles']) && auth()->check()) {
                $allowedRoles = is_array($rule['roles']) ? $rule['roles'] : explode(',', $rule['roles']);
                $allowedRoles = array_map('trim', $allowedRoles);

                if (! auth()->user()->hasRole($allowedRoles)) {
                    continue;
                }
            }

            if ($this->evaluateCondition($rule['condition'] ?? 'default', $tempReferral)) {
                $rate = $rule['commission_rate'] ?? $offering->commission_rate;

                return [
                    'rate' => $rate,
                    'label' => $rule['label'] ?? 'Rule '.($index + 1),
                    'commission' => $this->calculateBaseCommission($dealValue, $rate),
                    'rule_matched' => $index,
                ];
            }
        }

        // Fallback
        return [
            'rate' => $offering->commission_rate,
            'label' => 'Base Rate (Fallback)',
            'commission' => $this->calculateBaseCommission($dealValue, $offering->commission_rate),
            'rule_matched' => null,
        ];
    }

    /**
     * Validate commission rules structure.
     *
     * @return array [bool $isValid, array $errors]
     */
    public function validateRules(array $rules): array
    {
        $errors = [];

        foreach ($rules as $index => $rule) {
            // Check required fields
            if (! isset($rule['commission_rate'])) {
                $errors[] = 'Rule #'.($index + 1).': commission_rate is required';
            }

            if (! isset($rule['condition'])) {
                $errors[] = 'Rule #'.($index + 1).': condition is required';
            } else {
                // Validate condition format
                $condition = $rule['condition'];
                if ($condition !== 'default') {
                    $pattern = '/^(\w+)\s*(>=|<=|>|<|==|!=)\s*(\d+(?:\.\d+)?)$/';
                    if (! preg_match($pattern, trim($condition))) {
                        $errors[] = 'Rule #'.($index + 1).': invalid condition format';
                    }
                }
            }

            // Validate commission_rate is numeric and reasonable
            if (isset($rule['commission_rate'])) {
                $rate = $rule['commission_rate'];
                if (! is_numeric($rate) || $rate < 0 || $rate > 100) {
                    $errors[] = 'Rule #'.($index + 1).': commission_rate must be between 0 and 100';
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get all applicable rules for a referral with their evaluation results.
     * Useful for debugging/reporting.
     */
    public function debugRules(Referral $referral): array
    {
        $offering = $referral->offering;

        if (! $offering || empty($offering->commission_rules)) {
            return [
                'has_rules' => false,
                'base_rate' => $offering->commission_rate ?? 0,
                'rules' => [],
            ];
        }

        $results = [];

        foreach ($offering->commission_rules as $index => $rule) {
            $matches = $this->evaluateCondition($rule['condition'] ?? 'default', $referral);

            $results[] = [
                'index' => $index,
                'label' => $rule['label'] ?? 'Rule '.($index + 1),
                'condition' => $rule['condition'] ?? 'default',
                'commission_rate' => $rule['commission_rate'] ?? $offering->commission_rate,
                'matches' => $matches,
                'would_apply' => $matches,
            ];
        }

        return [
            'has_rules' => true,
            'base_rate' => $offering->commission_rate,
            'deal_value' => $referral->deal_value,
            'rules_evaluated' => $results,
            'final_rate' => $this->calculateCommission($referral),
        ];
    }
}
