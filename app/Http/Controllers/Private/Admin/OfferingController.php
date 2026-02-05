<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Categories\GetActiveCategoriesAction;
use App\Actions\Offerings\CreateOfferingAction;
use App\Actions\Offerings\GetOfferingsAction;
use App\Actions\Offerings\UpdateOfferingAction;
use App\Data\Offerings\OfferingData;
use App\Http\Requests\Admin\OfferingRequest;
use App\Models\Offering;
use Inertia\Inertia;
use Spatie\LaravelData\PaginatedDataCollection;

class OfferingController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Offering::class, 'offering');
    }

    public function index(GetOfferingsAction $action)
    {
        $offerings = $action->execute();

        return Inertia::render('Offerings/Index', [
            'offerings' => OfferingData::collect($offerings, PaginatedDataCollection::class),
        ]);
    }

    public function create(GetActiveCategoriesAction $categoriesAction)
    {
        return Inertia::render('Offerings/Create', [
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
        return $this->renderEdit($offering, 'Offerings', 'offering', [
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