<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Associates\GetAssociatesAction;
use App\Actions\Offerings\GetOfferingByIdAction;
use App\Actions\Offerings\GetOfferingsAction;
use App\Actions\Referrals\GetReferralPipelineAction;
use App\Actions\Referrals\GetReferralsAction;
use App\Actions\Referrals\SubmitReferralAction;
use App\Actions\Referrals\UpdateReferralStatusAction;
use App\Data\Offerings\OfferingData;
use App\Data\Referrals\ReferralPipelineData;
use App\Enums\ReferralStatus;
// Enum no longer needed here
use App\Http\Requests\Admin\ReferralRequest;
use App\Models\Referral;
use Inertia\Inertia;

class ReferralController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Referral::class, 'referral');
    }

    public function index(ReferralRequest $request, GetReferralsAction $action)
    {
        $filters = $request->only(['search', 'status', 'offering_id', 'associate_id', 'sector_id']);

        return Inertia::render('Private/Admin/Referrals/Index', [
            'referrals' => $action->execute($request->user(), $filters),
            'filters' => $filters,
            'statuses' => collect(ReferralStatus::cases())->map(fn ($s) => [
                'id' => $s->value,
                'name' => $s->value,
            ]),
            'offerings' => \App\Models\Offering::active()->get(['id', 'name'])->map(fn ($o) => [
                'id' => $o->id,
                'name' => $o->name,
            ]),
            'associates' => \App\Models\Associate::with('user')->get()->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->user?->name ?? 'Sistema',
                'email' => $a->user?->email,
            ]),
            'sectors' => \App\Models\Sector::all(['id', 'name'])->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
            ]),
        ]);
    }

    public function create(
        ReferralRequest $request,
        GetOfferingsAction $offeringsAction,
        GetOfferingByIdAction $offeringByIdAction,
        GetAssociatesAction $getAssociatesAction
    ) {
        $offeringId = $request->validated('offering_id') ?? $request->query('offering_id');
        $offering = null;

        if ($offeringId) {
            $offering = $offeringByIdAction->execute((int) $offeringId);
        }

        return Inertia::render('Private/Admin/Referrals/Create', [
            'offering' => $offering ? OfferingData::fromModel($offering) : null,
            'offerings' => $offeringId ? [] : OfferingData::collect($offeringsAction->execute($request->user(), false, [], false)),
            'associates' => $request->user()->isAdmin() ? $getAssociatesAction->execute() : [],
            'sectors' => \App\Models\Sector::all(['id', 'name']),
        ]);
    }

    public function store(ReferralRequest $request, SubmitReferralAction $action)
    {
        if ($request->user()->isAdmin()) {
            $associateId = $request->validated('associate_id');
        } else {
            $associate = $request->user()->associate;
            if (! $associate) {
                abort(403, 'Solo los asociados pueden crear referidos.');
            }
            $associateId = $associate->id;
        }

        $message = $action->execute(
            $request->toStoreData($associateId),
            $request->all()['form_data'] ?? []
        );

        return $this->redirectAfterStore('admin.referrals', $message);
    }

    public function show(Referral $referral)
    {
        return $this->renderShow($referral->load(['offering', 'commissions', 'history', 'associate', 'fileAssets']), 'Private/Admin/Referrals', 'referral');
    }

    public function pipeline(ReferralRequest $request, GetReferralPipelineAction $action)
    {
        $filters = $request->only(['category_id', 'sector_id']);

        return Inertia::render('Private/Admin/Referrals/Pipeline', [
            'referrals' => ReferralPipelineData::collect($action->execute($request->user(), $filters)),
            'categories' => \App\Models\Category::active()->get(['id', 'name']),
            'sectors' => \App\Models\Sector::all(['id', 'name']),
            'filters' => $filters,
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
