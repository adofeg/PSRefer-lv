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
    public function index(CommissionOverrideRequest $request, GetCommissionOverridesAction $action, \App\Actions\Associates\GetAssociatesAction $getAssociatesAction)
    {
        $this->authorize('viewAny', CommissionOverride::class);

        $query = CommissionOverride::with(['associate.user', 'offering'])
            ->latest();

        if ($request->has('associate_id')) {
            $query->where('associate_id', $request->associate_id);
        }

        // Return JSON for the Modal (axios)
        if ($request->wantsJson()) {
            return response()->json($query->get());
        }

        // Return Inertia Page for the Admin View
        return \Inertia\Inertia::render('Private/Admin/Commissions/Overrides', [
            'overrides' => $query->paginate(10),
            'associates' => $getAssociatesAction->execute(),
            'offerings' => \App\Models\Offering::select('id', 'name')->get()
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