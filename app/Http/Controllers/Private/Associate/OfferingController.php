<?php

namespace App\Http\Controllers\Private\Associate;

use App\Http\Requests\Associate\OfferingRequest;
use App\Models\Offering;
use App\Actions\Offerings\GetOfferingsAction;
use App\Data\Offerings\OfferingData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class OfferingController extends AssociateController
{
    public function index(OfferingRequest $request, GetOfferingsAction $action)
    {
        $user = Auth::user();

        $offerings = $action->execute(
            $user,
            false,
            $request->only(['search'])
        )->through(fn ($offering) => OfferingData::fromModel($offering, $user));

        return Inertia::render('Private/Associate/Offerings/Index', [
            'offerings' => $offerings,
            'filters' => $request->only(['search']),
        ]);
    }

    public function show(Offering $offering, GetOfferingsAction $action)
    {
        $user = Auth::user();

        // Use the action's logic to verify visibility (conflict of interest)
        $filters = ['search' => $offering->name]; // Simple filter to use the unified logic
        $isVisible = $action->execute($user, false, $filters, false)
            ->contains('id', $offering->id);

        if (! $isVisible) {
            abort(403, 'No tienes permiso para ver esta oferta debido a un conflicto de intereses.');
        }

        return Inertia::render('Private/Associate/Offerings/Show', [
            'offering' => OfferingData::fromModel($offering->load('owner'), $user),
        ]);
    }
}
