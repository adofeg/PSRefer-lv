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
    public function __construct(
        protected \App\Services\AuditService $auditService
    )
    {
        $this->authorizeResource(Offering::class, 'offering');
    }

    public function index(Request $request, GetOfferingsAction $action, GetActiveCategoriesAction $categoriesAction)
    {
        $user = $request->user();
        $includeInactive = $user?->hasRole(RoleName::adminRoles()) ?? false;
        
        $filters = $request->only(['search', 'category', 'status']);
        
        $offerings = $action->execute($user, $includeInactive, $filters);

        if ($request->boolean('json') || $request->wantsJson()) {
            return response()->json(
                OfferingData::collect($offerings, PaginatedDataCollection::class)
            );
        }

        return Inertia::render('Private/Admin/Offerings/Index', [
            'offerings' => OfferingData::collect($offerings, PaginatedDataCollection::class),
            'filters' => $filters,
            'categories' => $categoriesAction->execute()
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
        return Inertia::render('Private/Admin/Offerings/Edit', [
            'offering' => OfferingData::fromModel($offering),
            'categories' => $categoriesAction->execute()
        ]);
    }

    public function update(OfferingRequest $request, Offering $offering, UpdateOfferingAction $action)
    {
        $action->execute($offering, $request->toData());

        return $this->redirectAfterUpdate('admin.offerings', 'Offering updated.');
    }

    public function destroy(Offering $offering)
    {
        $this->auditService->logAction(
            $offering,
            'DELETE',
            "Offering '{$offering->name}' deleted by Admin"
        );

        $offering->delete();
        return redirect()->back()->with('success', 'Oferta eliminada correctamente.');
    }

    public function toggleStatus(Offering $offering, UpdateOfferingAction $action)
    {
        $this->authorize('update', $offering);
        
        $isActive = request()->boolean('is_active');
        $action->updateStatus($offering, $isActive);

        return back()->with('success', $isActive ? 'Oferta activada correctamente.' : 'Oferta desactivada correctamente.');
    }
}