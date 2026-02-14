<?php

namespace App\Http\Controllers\Private\Admin;

use App\Actions\Associates\GetAssociatesAction;
use App\Actions\Commissions\DeleteCommissionOverrideAction;
use App\Actions\Commissions\UpsertCommissionOverrideAction;
use App\Http\Requests\Admin\CommissionOverrideRequest;
use App\Models\CommissionOverride;
use App\Models\Offering;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

class CommissionOverrideController extends AdminController
{
    public function index(CommissionOverrideRequest $request, GetAssociatesAction $getAssociatesAction)
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
        return Inertia::render('Private/Admin/Commissions/Overrides', [
            'overrides' => $query->paginate(10),
            'associates' => $getAssociatesAction->execute(),
            'offerings' => Offering::select('id', 'name')->get(),
        ]);
    }

    public function store(CommissionOverrideRequest $request, UpsertCommissionOverrideAction $action): RedirectResponse
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

    public function destroy(CommissionOverride $override, DeleteCommissionOverrideAction $action): RedirectResponse
    {
        $this->authorize('delete', $override);

        $action->execute($override);

        return redirect()->back()->with('success', 'Excepción eliminada.');
    }
}
