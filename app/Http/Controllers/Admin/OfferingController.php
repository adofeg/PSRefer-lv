<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Offerings\GetOfferingsAction;
use App\Actions\Offerings\CreateOfferingAction;
use App\Actions\Offerings\UpdateOfferingAction;
use App\Data\Offerings\OfferingData;
use App\Http\Requests\Admin\OfferingRequest;
use App\Models\Offering;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            'offerings' => OfferingData::collect($offerings, \Spatie\LaravelData\PaginatedDataCollection::class),
        ]);
    }

    public function create()
    {
        return Inertia::render('Offerings/Create');
    }

    public function store(OfferingRequest $request, CreateOfferingAction $action)
    {
        $action->execute($request->validated(), $request->user()->id);
        return $this->redirectAfterStore('admin.offerings', 'Offering created.');
    }

    public function edit(Offering $offering)
    {
        return $this->renderEdit($offering, 'Offerings', 'offering', [
            'offering' => OfferingData::fromModel($offering)
        ]);
    }

    public function update(OfferingRequest $request, Offering $offering, UpdateOfferingAction $action)
    {
        $action->execute($offering, $request->validated());
        return $this->redirectAfterUpdate('admin.offerings', 'Offering updated.');
    }
}
