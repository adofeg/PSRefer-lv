<?php

namespace App\Actions\Referrals;

use App\Data\Referrals\ReferralStatusUpdateData;
use App\Enums\ReferralStatus;
use App\Mail\ReferralStatusUpdatedMail;
use App\Models\Referral;
use App\Services\AuditService;
use App\Services\CommissionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            $contractId = $data->contract_id ?? $referral->contract_id;
            if (! $contractId && $status === ReferralStatus::Closed->value) {
                $contractId = 'CTR-'.now()->format('Ym').'-'.str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
            }

            $referral->update([
                'status' => $status,
                'contract_id' => $contractId,
                'reminder_date' => $data->reminder_date ?? $referral->reminder_date,
                'closed_at' => $status === ReferralStatus::Closed->value ? now() : $referral->closed_at,
            ]);

            // Removed Agency Fee Validation ("Loss Prevention") as per business request.
            // If commission > agency_fee, the system should still allow the closing.

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
                logger()->error('Failed to send status update mail: '.$e->getMessage());
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

        if (! $offering || ! $referral->associate_id) {
            return;
        }

        // Create commissions (default status: Pending)
        $this->commissionService->createAllCommissions($referral, $offering);

        // NOTE: Auto-pay logic removed. Commissions are now always created as Pending.
        // Admin must manually approve comissions and upload receipt.
    }
}
