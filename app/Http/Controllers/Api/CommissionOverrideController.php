<?php

namespace App\Http\Controllers\Api;

use App\Actions\Commissions\DeleteCommissionOverrideAction;
use App\Actions\Commissions\GetCommissionOverridesAction;
use App\Actions\Commissions\UpsertCommissionOverrideAction;
use App\Http\Requests\Api\CommissionOverrideRequest;
use App\Models\CommissionOverride;

class CommissionOverrideController extends ApiController
{
    public function index(CommissionOverrideRequest $request, GetCommissionOverridesAction $action)
    {
        $this->authorize('viewAny', CommissionOverride::class);

        $data = $request->toQueryData();

        return response()->json($action->execute($data->associate_id));
    }

    public function store(CommissionOverrideRequest $request, UpsertCommissionOverrideAction $action)
    {
        $this->authorize('create', CommissionOverride::class);

        $data = $request->toUpsertData();

        return response()->json($action->execute(
            $data->associate_id,
            $data->offering_id,
            $data->commission_rate
        ));
    }

    public function destroy(CommissionOverride $override, DeleteCommissionOverrideAction $action)
    {
        $this->authorize('delete', $override);

        $action->execute($override);

        return response()->json(['message' => 'Override deleted']);
    }
}
