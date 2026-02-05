<?php

namespace App\Actions\Referrals;

use App\Models\Referral;
use App\Models\User;
use App\Services\AuditService;
use App\Services\CommissionService;
use Illuminate\Support\Facades\DB;

class UpdateReferralStatusAction
{
    public function __construct(
        protected AuditService $auditService,
        protected CommissionService $commissionService
    ) {}

    public function execute(Referral $referral, string $status, User $actor, array $data = []): Referral
    {
        $oldStatus = $referral->status;

        if ($oldStatus === $status) {
            return $referral;
        }

        return DB::transaction(function () use ($referral, $status, $actor, $data, $oldStatus) {
            $referral->update([
                'status' => $status,
                'deal_value' => $data['deal_value'] ?? $referral->deal_value,
                'revenue_generated' => $data['revenue_generated'] ?? $referral->revenue_generated,
            ]);

            $this->auditService->logReferralStatusChange(
                $referral->id,
                $actor->id,
                $oldStatus,
                $status,
                $data['notes'] ?? ''
            );

            // Handle Commission Trigger
            if ($status === 'Cerrado') {
                $this->handleClosedReferral($referral);
            }

            return $referral->fresh();
        });
    }

    protected function handleClosedReferral(Referral $referral)
    {
        $offering = $referral->offering;
        if (!$offering) return;

        // Check for User Override
        $override = DB::table('user_offering_commissions')
            ->where('user_id', $referral->user_id)
            ->where('offering_id', $referral->offering_id)
            ->first();

        $this->commissionService->createAllCommissions($referral, $offering, $override);
    }
}
