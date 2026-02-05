<?php

namespace App\Actions\Referrals;

use App\Data\Referrals\ReferralStatusUpdateData;
use App\Enums\ReferralStatus;
use App\Models\Referral;
use App\Services\AuditService;
use App\Services\CommissionService;
use App\Mail\ReferralStatusUpdatedMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UpdateReferralStatusAction
{
    public function __construct(
        protected AuditService $auditService,
        protected CommissionService $commissionService
    ) {}

    public function execute(Referral $referral, ReferralStatusUpdateData $data, $actor): Referral
    {
        $oldStatus = $referral->status;
        $status = $data->status->value;

        if ($oldStatus === $status) {
            return $referral;
        }

        return DB::transaction(function () use ($referral, $status, $actor, $data, $oldStatus) {
            $referral->update([
                'status' => $status,
                'deal_value' => $data->deal_value ?? $referral->deal_value,
                'revenue_generated' => $data->revenue_generated ?? $referral->revenue_generated,
                'contract_id' => $data->contract_id ?? $referral->contract_id,
                'payment_method' => $data->payment_method ?? $referral->payment_method,
                'down_payment' => $data->down_payment ?? $referral->down_payment,
                'agency_fee' => $data->agency_fee ?? $referral->agency_fee,
            ]);

            $this->auditService->logReferralStatusChange(
                $referral->id,
                $actor,
                $oldStatus,
                $status,
                $data->notes ?? ''
            );
            
            // Send Email Notification to Associate
            try {
                Mail::to($referral->associate?->user?->email)->send(new ReferralStatusUpdatedMail(
                    $referral->associate?->user?->name ?? 'Asociado',
                    $referral->client_name,
                    $status,
                    $data->notes ?? null
                ));
            } catch (\Exception $e) {
                // Log error but don't fail transaction if mail fails
                logger()->error('Failed to send status update mail: ' . $e->getMessage());
            }

            // Handle Commission Trigger
            if ($status === ReferralStatus::Closed->value) {
                $this->handleClosedReferral($referral);
            }

            return $referral->fresh();
        });
    }

    protected function handleClosedReferral(Referral $referral)
    {
        $offering = $referral->offering;
        if (!$offering) return;

        if (!$referral->associate_id) {
            return;
        }

        // Check for User Override
        $override = DB::table('commission_overrides')
            ->where('associate_id', $referral->associate_id)
            ->where('offering_id', $referral->offering_id)
            ->where('is_active', true)
            ->first();

        $commissions = $this->commissionService->createAllCommissions($referral, $offering, $override);
        
        // Parity with JS: Increment balance for one-time commissions
        $totalToIncrement = collect($commissions)->where('recurrence_type', 'one_time')->sum('amount');
        if ($totalToIncrement > 0) {
            $referral->associate?->increment('balance', $totalToIncrement);
        }
    }
}
