<?php

namespace App\Http\Controllers\Private\Associate;

use App\Http\Requests\Associate\OfferingRequest;
use App\Models\Offering;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;

class OfferingController extends AssociateController
{
    public function index(OfferingRequest $request)
    {
        $user = Auth::user();
        $associate = $user->associate; // Accessor

        $offerings = Offering::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%"); // Fix: search scope usage might be safer with custom query
            })
            ->when($associate && $associate->category, function ($query) use ($associate) {
                $query->excludeCategory($associate->category);
            })
            ->paginate(10)
            ->withQueryString()
            ->through(function ($offering) use ($user) {
                // Determine the correct ID for the referrer (Associate ID)
                // $user->id is the User ID. The 'ref' parameter expects Associate ID in PublicController (via 'ref' exists:associates,id).
                // Let's verify if 'ref' should be User ID or Associate ID.
                // PublicController: $referrerId = $request->query('ref'); ... Associate::find($referrerId);
                // So it expects ASSOCIATE ID.

                $associateId = $user->profileable_id; // Assuming user is associate

                $offering->share_url = URL::signedRoute(
                    'site.invite',
                    ['offeringId' => $offering->id, 'ref' => $associateId]
                );

                return $offering;
            });

        return Inertia::render('Private/Associate/Offerings/Index', [
            'offerings' => $offerings,
            'filters' => $request->only(['search']),
        ]);
    }

    public function show(Offering $offering)
    {
        // Security check: Ensure the offering is not in the excluded category
        $user = Auth::user();
        $associateCategory = $user->category;

        if ($associateCategory && $offering->category === $associateCategory) {
            abort(403, 'No tienes permiso para ver esta oferta debido a un conflicto de intereses.');
        }

        $associateId = $user->profileable_id;
        $offering->share_url = URL::signedRoute(
            'site.invite',
            ['offeringId' => $offering->id, 'ref' => $associateId]
        );

        return Inertia::render('Private/Associate/Offerings/Show', [
            'offering' => $offering->load('owner'),
        ]);
    }
}
