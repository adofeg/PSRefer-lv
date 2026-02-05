<?php

namespace App\Actions\Public;

use App\Data\Public\OfferingApplicationData;
use App\Enums\ReferralStatus;
use App\Models\Offering;
use App\Models\Referral;
use App\Services\FormSchemaValidator;

class SubmitOfferingApplicationAction
{
    public function __construct(
        protected FormSchemaValidator $validator,
        protected TrackReferralClickAction $trackReferralClickAction
    ) {}

    public function execute(
        int $offeringId,
        OfferingApplicationData $data,
        $request,
        string $source = 'public_landing',
        string $linkType = 'conversion'
    ): array
    {
        $createdReferrals = [];
        $offering = Offering::where('id', $offeringId)
            ->where('is_active', true)
            ->firstOrFail();

        $formData = [];
        if ($offering->form_schema && !empty($offering->form_schema)) {
            $formData = $this->validator->validate(
                $offering->form_schema,
                $data->form_data ?? []
            );
        }

        $assignedAssociateId = $data->referrer_id ?? null;

        if ($offering->name === 'Referencia General' && isset($formData['Servicios de Interés']) && is_array($formData['Servicios de Interés'])) {
            $selectedServices = $formData['Servicios de Interés'];
            $targetOfferings = Offering::whereIn('name', $selectedServices)->get();

            foreach ($targetOfferings as $target) {
                $createdReferrals[] = Referral::create([
                    'associate_id' => $assignedAssociateId,
                    'offering_id' => $target->id,
                    'client_name' => $data->client_name,
                    'client_contact' => $data->client_contact,
                    'metadata' => array_merge(
                        ['source' => $source, 'origen' => 'Referencia General'],
                        $formData
                    ),
                    'notes' => "[Ref. General] " . ($data->notes ?? ''),
                    'status' => ReferralStatus::Prospect->value,
                ]);
            }

            if ($data->referrer_id) {
                $this->trackReferralClickAction->execute($data->referrer_id, $offeringId, $request, $linkType);
            }

            return $this->formatReferralsResponse($createdReferrals);
        }

        $createdReferrals[] = Referral::create([
            'associate_id' => $assignedAssociateId,
            'offering_id' => $offering->id,
            'client_name' => $data->client_name,
            'client_contact' => $data->client_contact,
            'metadata' => array_merge(
                ['source' => $source],
                $formData
            ),
            'notes' => $data->notes,
            'status' => ReferralStatus::Prospect->value,
        ]);

        if ($data->referrer_id) {
            $this->trackReferralClickAction->execute($data->referrer_id, $offeringId, $request, $linkType);
        }

        return $this->formatReferralsResponse($createdReferrals);
    }

    protected function formatReferralsResponse(array $referrals): array
    {
        return array_map(function (Referral $referral) {
            return [
                'id' => (int) $referral->id,
                'client_name' => $referral->client_name,
                'status' => $referral->status,
                'created_at' => $referral->created_at?->toISOString(),
            ];
        }, $referrals);
    }
}
