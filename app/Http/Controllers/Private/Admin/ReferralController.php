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
use App\Http\Requests\Admin\ReferralRequest;
use App\Models\Referral;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReferralController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Referral::class, 'referral');
    }

    public function index(Request $request, GetReferralsAction $action)
    {
        $filters = $request->only(['search', 'status']);
        
        return Inertia::render('Private/Admin/Referrals/Index', [
            'referrals' => $action->execute($request->user(), $filters),
            'filters' => $filters,
            'statuses' => ReferralStatus::cases()
        ]);
    }

    public function create(
        ReferralRequest $request,
        GetOfferingsAction $offeringsAction,
        GetOfferingByIdAction $offeringByIdAction,
        \App\Actions\Associates\GetAssociatesAction $getAssociatesAction
    )
    {
        $offeringId = $request->validated('offering_id') ?? $request->query('offering_id');
        $offering = null;

        if ($offeringId) {
            $offering = $offeringByIdAction->execute((int) $offeringId);
        }

        return Inertia::render('Private/Admin/Referrals/Create', [
            'offering' => $offering ? OfferingData::fromModel($offering) : null,
            'offerings' => $offeringId ? [] : OfferingData::collect($offeringsAction->execute($request->user(), false)),
            'associates' => \App\Enums\RoleName::isAdmin($request->user()) ? $getAssociatesAction->execute() : [],
        ]);
    }

    public function store(ReferralRequest $request, SubmitReferralAction $action)
    {
        if (\App\Enums\RoleName::isAdmin($request->user())) {
            $associateId = $request->validated('associate_id');
        } else {
            $associate = $request->user()->associateProfile();
            if (!$associate) {
                abort(403, 'Solo los asociados pueden crear referidos.');
            }
            $associateId = $associate->id;
        }

        $message = $action->execute($request, (int) $associateId);

        return $this->redirectAfterStore('admin.referrals', $message);
    }

    public function show(Referral $referral)
    {
        return $this->renderShow($referral->load(['offering', 'commissions', 'history', 'associate']), 'Private/Admin/Referrals', 'referral');
    }

    public function pipeline(GetReferralPipelineAction $action)
    {
        return Inertia::render('Private/Admin/Referrals/Pipeline', [
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

    public function destroy(Referral $referral)
    {
        $referral->delete();
        return back()->with('success', 'Referido eliminado correctamente.');
    }
}