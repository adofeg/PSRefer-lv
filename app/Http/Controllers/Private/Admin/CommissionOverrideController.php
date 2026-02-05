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
    public function index(CommissionOverrideRequest $request, GetCommissionOverridesAction $action): JsonResponse
    {
        $data = $request->toQueryData();

        return response()->json($action->execute($data->associate_id));
    }

    public function store(CommissionOverrideRequest $request, UpsertCommissionOverrideAction $action): JsonResponse
    {
        $data = $request->toUpsertData();

        return response()->json($action->execute(
            $data->associate_id,
            $data->offering_id,
            $data->commission_rate
        ));
    }

    public function destroy(CommissionOverride $override, DeleteCommissionOverrideAction $action): JsonResponse
    {
        $action->execute($override);

        return response()->json(['message' => 'Override deleted']);
    }
}