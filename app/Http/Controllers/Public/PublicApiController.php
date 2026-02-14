<?php

namespace App\Http\Controllers\Public;

use App\Actions\Public\GetPublicOfferingInfoAction;
use App\Actions\Public\GetPublicUserInfoAction;
use App\Actions\Public\SubmitPublicLeadAction;
use App\Actions\Public\TrackReferralClickAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\PublicApiRequest;
use Illuminate\Http\JsonResponse;

class PublicApiController extends Controller
{
    public function userInfo(int $id, GetPublicUserInfoAction $action): JsonResponse
    {
        return response()->json($action->execute($id));
    }

    public function offeringInfo(int $id, GetPublicOfferingInfoAction $action): JsonResponse
    {
        return response()->json($action->execute($id));
    }

    public function submitLead(PublicApiRequest $request, SubmitPublicLeadAction $action): JsonResponse
    {
        $result = $action->execute(
            (int) $request->validated('offering_id'),
            $request->toLeadData(),
            $request
        );

        return response()->json($result, 201);
    }

    public function trackClick(PublicApiRequest $request, TrackReferralClickAction $action): JsonResponse
    {
        $data = $request->toClickData();

        $action->execute(
            $data['referrer_id'],
            $data['offering_id'],
            $request,
            $data['link_type']
        );

        return response()->json(['message' => 'Click recorded']);
    }
}
