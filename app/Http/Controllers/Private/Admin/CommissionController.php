<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Associates\GetAssociatesAction;
use App\Actions\Commissions\CreateCommissionAction;
use App\Actions\Commissions\GetAllCommissionsAction;
use App\Actions\Commissions\GetCommissionStatsAction;
use App\Actions\Commissions\UpdateCommissionAction;
use App\Enums\CommissionStatus;
use App\Http\Requests\Admin\CommissionRequest;
use App\Models\Commission;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CommissionController extends AdminController
{
    public function __construct()
    {
        $this->authorizeResource(Commission::class, 'commission');
    }

    public function index(CommissionRequest $request, GetAllCommissionsAction $action): Response
    {
        $filters = $request->only(['search', 'status', 'associate_id']);

        return Inertia::render('Private/Admin/Commissions/Index', [
            'commissions' => $action->execute($filters),
            'filters' => $filters,
            'statuses' => array_column(CommissionStatus::cases(), 'value'),
        ]);
    }

    public function create(GetAssociatesAction $getAssociatesAction): Response
    {
        return Inertia::render('Private/Admin/Commissions/Create', [
            'associates' => $getAssociatesAction->execute(),
            'statuses' => array_column(CommissionStatus::cases(), 'value'),
            // Referrals will be fetched dynamically or we pass all? Better dynamic or list recent.
            // For now, let's keep it simple: Select Associate first, then maybe fetch referrals if needed.
        ]);
    }

    public function store(CommissionRequest $request, CreateCommissionAction $action)
    {
        $action->execute($request->validated());
        return redirect()->route('admin.commissions.index')->with('success', 'Comisión registrada exitosamente.');
    }

    public function edit(Commission $commission, GetAssociatesAction $getAssociatesAction): Response
    {
        return Inertia::render('Private/Admin/Commissions/Edit', [
            'commission' => $commission->load(['associate.user', 'referral.offering']),
            'associates' => $getAssociatesAction->execute(),
            'statuses' => array_column(CommissionStatus::cases(), 'value'),
        ]);
    }

    public function update(CommissionRequest $request, Commission $commission, UpdateCommissionAction $action)
    {
        $action->execute($commission, $request->validated());
        return redirect()->route('admin.commissions.index')->with('success', 'Comisión actualizada exitosamente.');
    }

    public function destroy(Commission $commission): RedirectResponse
    {
        // Instead of hard delete, we void it.
        $commission->update(['status' => 'void']);
        return redirect()->route('admin.commissions.index')->with('success', 'Comisión anulada correctamente.');
    }

    public function report(GetCommissionStatsAction $action): Response
    {
        $this->authorize('viewAny', Commission::class);
        
        return Inertia::render('Private/Admin/Commissions/Report', [
            'stats' => $action->execute()
        ]);
    }
}
