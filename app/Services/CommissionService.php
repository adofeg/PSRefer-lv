<?php

namespace App\Services;

use App\Enums\CommissionStatus;
use App\Models\Commission;
use App\Models\CommissionOverride;
use App\Models\Offering;
use App\Models\Referral;

class CommissionService
{
    protected $ruleEngine;

    public function __construct(CommissionRuleEngine $ruleEngine)
    {
        $this->ruleEngine = $ruleEngine;
    }

    /**
     * Calculate commissions based on offering config, rules, and override
     */
    public function calculateCommissions(Referral $referral, Offering $offering, $override = null)
    {
        if (! $override) {
            $override = CommissionOverride::where('associate_id', $referral->associate_id)
                ->where(function ($query) use ($offering) {
                    $query->where('offering_id', $offering->id)
                        ->orWhereNull('offering_id');
                })
                ->where('is_active', true)
                ->orderByRaw('offering_id DESC') // Specific (ID) first, then Global (NULL)
                ->first();
        }

        $commissions = [];
        $dealValue = $referral->deal_value ?? $referral->revenue_generated ?? 0;

        // Offering Config
        $config = $offering->commission_config ?? [];

        // 1. Override (Highest Priority)
        if ($override && isset($override->base_commission)) {
            $percentage = $override->base_commission;
            $amount = $dealValue > 0 ? ($dealValue * $percentage) / 100 : 0;

            $commissions[] = [
                'type' => 'override',
                'amount' => round($amount, 2),
                'percentage' => $percentage,
                'recurrence_type' => 'one_time',
            ];

            return $commissions;
        }

        // 2. Rule Engine (Dynamic Rules)
        if ($dealValue > 0) {
            $matchingRulePreview = $this->ruleEngine->previewCommissionRate($offering, $dealValue);
            
            // Check if it's actually a rule match and not just a fallback in the engine
            // In the engine, 'rule_matched' is null if it just returned the base rate.
            // But we want to check if calculateCommission returns something based on a rule.
            $amount = $this->ruleEngine->calculateCommission($referral);
            
            // If the rule engine found a specific match (preview has a rule_matched index), we use it.
            if ($matchingRulePreview['rule_matched'] !== null) {
                $commissions[] = [
                    'type' => 'percentage',
                    'amount' => $amount,
                    'percentage' => $matchingRulePreview['rate'],
                    'recurrence_type' => 'one_time',
                    'rule_label' => $matchingRulePreview['label'] ?? null,
                ];

                return $commissions;
            }
        }

        // 3. Base Commission by Type (Fallback if no rules match)
        if ($offering->commission_type === 'fixed') {
            $amount = $offering->base_commission;
            
            if ($amount > 0) {
                $commissions[] = [
                    'type' => 'fixed',
                    'amount' => floatval($amount),
                    'percentage' => 0,
                    'recurrence_type' => 'one_time',
                ];

                return $commissions;
            }
        } elseif ($offering->commission_type === 'percentage') {
            $percentage = $offering->base_commission;
            
            if ($percentage > 0 && $dealValue > 0) {
                $amount = ($dealValue * $percentage) / 100;
                $commissions[] = [
                    'type' => 'percentage',
                    'amount' => round($amount, 2),
                    'percentage' => $percentage,
                    'recurrence_type' => 'one_time',
                ];

                return $commissions;
            }
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
        if (! $referral->associate_id) {
            return [];
        }

        $configs = $this->calculateCommissions($referral, $offering, $override);
        $created = [];

        foreach ($configs as $config) {
            $commission = Commission::create([
                'referral_id' => $referral->id,
                'associate_id' => $referral->associate_id,
                'amount' => $config['amount'],
                'commission_percentage' => $config['percentage'],
                'commission_type' => $config['type'],
                'recurrence_type' => $config['recurrence_type'],
                'recurrence_interval' => $config['recurrence_interval'] ?? null,
                'recurrence_end_date' => $config['recurrence_end_date'] ?? null,
                'status' => CommissionStatus::Pending->value,
            ]);
            $created[] = $commission;
        }

        return $created;
    }
}
