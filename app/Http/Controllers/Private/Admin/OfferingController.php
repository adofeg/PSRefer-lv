<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Categories\GetActiveCategoriesAction;
use App\Actions\Offerings\CreateOfferingAction;
use App\Actions\Offerings\GetOfferingsAction;
use App\Actions\Offerings\UpdateOfferingAction;
use App\Data\Offerings\OfferingData;
use App\Enums\RoleName;
use App\Http\Requests\Admin\OfferingRequest;
use App\Models\Offering;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\LaravelData\PaginatedDataCollection;

class OfferingController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Offering::class, 'offering');
    }

    public function index(Request $request, GetOfferingsAction $action)
    {
        $user = $request->user();
        $includeInactive = $user?->hasRole(RoleName::adminRoles()) ?? false;
        $offerings = $action->execute($user, $includeInactive);

        if ($request->boolean('json') || $request->wantsJson()) {
            return response()->json(
                OfferingData::collect($offerings, PaginatedDataCollection::class)
            );
        }

        return Inertia::render('Private/Admin/Offerings/Index', [
            'offerings' => OfferingData::collect($offerings, PaginatedDataCollection::class),
        ]);
    }

    public function create(GetActiveCategoriesAction $categoriesAction)
    {
        return Inertia::render('Private/Admin/Offerings/Create', [
            'categories' => $categoriesAction->execute()
        ]);
    }

    public function store(OfferingRequest $request, CreateOfferingAction $action)
    {
        $employeeId = $request->user()->employeeProfile()?->id;
        if (!$employeeId) {
            abort(403, 'Solo empleados pueden crear ofertas.');
        }

        $action->execute($request->toData(), $employeeId);
        return $this->redirectAfterStore('admin.offerings', 'Offering created.');
    }

    public function edit(Offering $offering, GetActiveCategoriesAction $categoriesAction)
    {
        return $this->renderEdit($offering, 'Private/Admin/Offerings', 'offering', [
            'offering' => OfferingData::fromModel($offering),
            'categories' => $categoriesAction->execute()
        ]);
    }

    public function update(OfferingRequest $request, Offering $offering, UpdateOfferingAction $action)
    {
        $action->execute($offering, $request->toData());

        return $this->redirectAfterUpdate('admin.offerings', 'Offering updated.');
    }
}