<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Offering;
use App\Models\Referral;
use Illuminate\Support\Facades\Log;

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
    public function calculateCommissions(Referral $referral, Offering $offering): array
    {
        $commissions = [];
        // 1. Rule Engine (Dynamic Rules - Variable/Override rules could apply)
        $amount = $this->ruleEngine->calculateCommission($referral);
        // Note: The rule engine complex logic was simplified as deal_value was removed.
        // If the rule engine found a specific match or override, we could use it here.
        // Currently falling back to standard commission types below.

        // 2. Base Commission by Type

        if ($offering->commission_type === \App\Enums\CommissionType::Fixed) {
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
        } elseif ($offering->commission_type === \App\Enums\CommissionType::Percentage) {
            $percentage = $offering->base_commission;
            // Always create a commission record for percentage, even if amount is 0,
            // so Admin can edit it later in the financial panel.
            $amount = 0; // The base referral no longer has a deal_value

            $commissions[] = [
                'type' => 'percentage',
                'amount' => round($amount, 2),
                'percentage' => $percentage,
                'recurrence_type' => 'one_time',
            ];

            return $commissions;
        } elseif ($offering->commission_type === \App\Enums\CommissionType::Variable) {
            // Variable/Manual commissions always start at $0 if not specified
            $commissions[] = [
                'type' => 'variable',
                'amount' => 0,
                'percentage' => 0,
                'recurrence_type' => 'one_time',
            ];

            return $commissions;
        }

        return $commissions;
    }

    public function createAllCommissions(Referral $referral, Offering $offering)
    {

        if (! $referral->associate_id) {
            Log::warning('CommissionService: No associate_id found for referral, skipping creation');

            return [];
        }

        $calculated = $this->calculateCommissions($referral, $offering);

        $created = [];

        foreach ($calculated as $item) {
            $commission = Commission::create([
                'referral_id' => $referral->id,
                'associate_id' => $referral->associate_id,
                'amount' => $item['amount'],
                'commission_percentage' => $item['percentage'],
                'commission_type' => $item['type'],
                'recurrence_type' => $item['recurrence_type'],
                'recurrence_interval' => $item['recurrence_interval'] ?? null,
                'recurrence_end_date' => $item['recurrence_end_date'] ?? null,
                'status' => \App\Enums\CommissionStatus::Pending->value,
            ]);
            $created[] = $commission;
        }

        return $created;
    }
}
