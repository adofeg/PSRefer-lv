<?php

namespace App\Http\Controllers\Private\Associate;

use App\Http\Requests\Associate\ReferralRequest;
use App\Models\Offering;
use App\Models\Referral;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReferralController extends AssociateController
{
    public function index(ReferralRequest $request)
    {
        $user = Auth::user();
        $associate = $user->associate;

        if (! $associate) {
            return Inertia::render('Private/Associate/Referrals/Index', [
                'referrals' => [],
                'filters' => $request->only(['search']),
            ]);
        }

        $referrals = Referral::query()
            ->where('associate_id', $associate->id)
            ->with(['offering'])
            ->when($request->search, function ($query, $search) {
                $query->searchByClient($search);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Private/Associate/Referrals/Index', [
            'referrals' => $referrals,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create(ReferralRequest $request)
    {
        $offeringId = $request->validated('offering_id');
        $selectedOffering = null;
        $user = Auth::user();
        $associate = $user->associate;

        if ($offeringId) {
            $selectedOffering = Offering::find($offeringId);
            if ($selectedOffering && $associate && $selectedOffering->category_id === $associate->category_id) {
                // Conflict
                $selectedOffering = null;
            }
        }

        $offerings = Offering::query()
            ->where('is_active', true)
            ->excludeCategory($associate?->category)
            ->get(['id', 'name', 'base_commission', 'commission_type', 'form_schema']);

        return Inertia::render('Private/Associate/Referrals/Create', [
            'offerings' => $offerings,
            'selectedOffering' => $selectedOffering,
        ]);
    }

    public function store(ReferralRequest $request, \App\Actions\Referrals\SubmitReferralAction $action)
    {
        $user = Auth::user();
        $associate = $user->associate;

        if (! $associate) {
            abort(403, 'Usuario no es un asociado válido.');
        }

        $message = $action->execute(
            $request->toStoreData($associate->id),
            $request->input('form_data', [])
        );

        return redirect()->route('associate.referrals.index')->with('success', 'Referido creado exitosamente.');
    }
}
