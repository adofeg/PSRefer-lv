<?php

namespace App\Actions\Public;

use App\Data\Public\OfferingApplicationData;
use Illuminate\Http\Request;

class SubmitPublicLeadAction
{
    public function __construct(
        protected SubmitOfferingApplicationAction $submitOfferingApplicationAction
    ) {}

    public function execute(int $offeringId, OfferingApplicationData $data, Request $request): array
    {
        $referrals = $this->submitOfferingApplicationAction->execute(
            $offeringId,
            $data,
            $request,
            source: 'public_link',
            linkType: 'public'
        );

        return [
            'message' => 'Solicitud recibida exitosamente',
            'referrals' => $referrals,
        ];
    }
}
