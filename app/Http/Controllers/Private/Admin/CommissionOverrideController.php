<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Commissions\DeleteCommissionOverrideAction;
use App\Actions\Commissions\GetCommissionOverridesAction;
use App\Actions\Commissions\UpsertCommissionOverrideAction;
use App\Http\Requests\Admin\CommissionOverrideRequest;
use App\Models\CommissionOverride;
use Illuminate\Http\JsonResponse;

class CommissionOverrideController extends AdminController
{
    public function index(CommissionOverrideRequest $request, GetCommissionOverridesAction $action, \App\Actions\Associates\GetAssociatesAction $getAssociatesAction): \Inertia\Response
    {
        $this->authorize('viewAny', CommissionOverride::class);

        // We want to list ALL overrides for the Admin UI, mostly.
        // The original action might be specific to finding overrides for calculation.
        // Let's grab all with pagination for the UI.
        
        $overrides = CommissionOverride::with(['associate.user', 'offering'])
            ->latest()
            ->paginate(10);
            
        return \Inertia\Inertia::render('Private/Admin/Commissions/Overrides', [
            'overrides' => $overrides,
            'associates' => $getAssociatesAction->execute(),
            'offerings' => \App\Models\Offering::select('id', 'name')->get() // Simple fetch for dropdown
        ]);
    }

    public function store(CommissionOverrideRequest $request, UpsertCommissionOverrideAction $action): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('create', CommissionOverride::class);

        $data = $request->toUpsertData();

        $action->execute(
            $data->associate_id,
            $data->offering_id,
            $data->commission_rate
        );
        
        return redirect()->back()->with('success', 'Excepción guardada correctamente.');
    }

    public function destroy(CommissionOverride $override, DeleteCommissionOverrideAction $action): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('delete', $override);

        $action->execute($override);

        return redirect()->back()->with('success', 'Excepción eliminada.');
    }
}