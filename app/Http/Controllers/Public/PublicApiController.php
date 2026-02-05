<?php

namespace App\Http\Controllers\Public;

use App\Actions\Public\GetPublicOfferingInfoAction;
use App\Actions\Public\GetPublicUserInfoAction;
use App\Actions\Public\SubmitPublicLeadAction;
use App\Actions\Public\TrackReferralClickAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\LeadSubmissionRequest;
use App\Http\Requests\Public\ReferralClickRequest;
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

    public function submitLead(LeadSubmissionRequest $request, SubmitPublicLeadAction $action): JsonResponse
    {
        $result = $action->execute(
            (int) $request->validated('offering_id'),
            $request->toData(),
            $request
        );

        return response()->json($result, 201);
    }

    public function trackClick(ReferralClickRequest $request, TrackReferralClickAction $action): JsonResponse
    {
        $data = $request->toData();

        $action->execute(
            $data['referrer_id'],
            $data['offering_id'],
            $request,
            $data['link_type']
        );

        return response()->json(['message' => 'Click recorded']);
    }
}