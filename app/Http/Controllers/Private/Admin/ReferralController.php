<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Offerings\GetOfferingByIdAction;
use App\Actions\Offerings\GetOfferingsAction;
use App\Actions\Referrals\GetReferralPipelineAction;
use App\Actions\Referrals\GetReferralsAction;
use App\Actions\Referrals\SubmitReferralAction;
use App\Actions\Referrals\UpdateReferralStatusAction;
use App\Data\Offerings\OfferingData;
use App\Data\Referrals\ReferralPipelineData;
use App\Enums\ReferralStatus;
use App\Http\Requests\Admin\ReferralCreateRequest;
use App\Http\Requests\Admin\ReferralRequest;
use App\Models\Referral;
use Inertia\Inertia;

class ReferralController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Referral::class, 'referral');
    }

    public function index(GetReferralsAction $action)
    {
        return Inertia::render('Referrals/Index', [
            'referrals' => $action->execute()
        ]);
    }

    public function create(
        ReferralCreateRequest $request,
        GetOfferingsAction $offeringsAction,
        GetOfferingByIdAction $offeringByIdAction
    )
    {
        $offeringId = $request->query('offering_id');
        $offering = null;

        if ($offeringId) {
            $offering = $offeringByIdAction->execute((int) $offeringId);
        }

        return Inertia::render('Referrals/Create', [
            'offering' => $offering ? OfferingData::fromModel($offering) : null,
            'offerings' => $offeringId ? [] : OfferingData::collect($offeringsAction->execute())
        ]);
    }

    public function store(ReferralRequest $request, SubmitReferralAction $action)
    {
        $associate = $request->user()->associateProfile();
        if (!$associate) {
            abort(403, 'Solo los asociados pueden crear referidos.');
        }

        $message = $action->execute($request, $associate->id);

        return $this->redirectAfterStore('admin.referrals', $message);
    }

    public function show(Referral $referral)
    {
        return $this->renderShow($referral->load(['offering', 'commissions', 'history', 'associate']), 'Referrals', 'referral');
    }

    public function pipeline(GetReferralPipelineAction $action)
    {
        return Inertia::render('Referrals/Pipeline', [
            'referrals' => ReferralPipelineData::collect($action->execute())
        ]);
    }

    public function update(ReferralRequest $request, Referral $referral, UpdateReferralStatusAction $action)
    {
        $action->execute(
            $referral,
            $request->toStatusUpdateData(ReferralStatus::from($referral->status)),
            $request->user()
        );

        return $this->redirectAfterUpdate('admin.referrals', 'Referral updated.');
    }
}