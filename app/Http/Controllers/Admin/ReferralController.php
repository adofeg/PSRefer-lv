<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Referrals\CreateReferralAction;
use App\Actions\Referrals\GetReferralsAction;
use App\Actions\Referrals\UpdateReferralStatusAction;
use App\Actions\Offerings\GetOfferingsAction;
use App\Data\Referrals\ReferralData;
use App\Data\Offerings\OfferingData;
use App\Http\Requests\Admin\ReferralRequest;
use App\Models\Offering;
use App\Models\Referral;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

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

    public function create(Request $request, GetOfferingsAction $offeringsAction)
    {
        $offeringId = $request->query('offering_id');
        $offering = null;

        if ($offeringId) {
            $offering = Offering::findOrFail($offeringId);
        }

        return Inertia::render('Referrals/Create', [
            'offering' => $offering,
            'offerings' => $offeringId ? [] : OfferingData::collect($offeringsAction->execute())
        ]);
    }

    public function store(ReferralRequest $request, CreateReferralAction $action)
    {
        $referralData = new ReferralData(
            client_name: $request->validated('client_name'),
            client_contact: $request->validated('client_contact'),
            offering_id: $request->validated('offering_id'),
            metadata: $request->validated('metadata'),
            notes: $request->validated('notes'),
            user_id: Auth::id()
        );

        $action->execute($referralData);

        return $this->redirectAfterStore('admin.referrals', 'Referral created successfully.');
    }

    public function show(Referral $referral)
    {
        return $this->renderShow($referral->load(['offering', 'commissions']), 'Referrals', 'referral');
    }

    public function update(ReferralRequest $request, Referral $referral, UpdateReferralStatusAction $action)
    {
        $action->execute(
            $referral,
            $request->validated('status') ?? $referral->status,
            $request->user(),
            $request->validated()
        );

        return $this->redirectAfterUpdate('admin.referrals', 'Referral updated.');
    }
}
