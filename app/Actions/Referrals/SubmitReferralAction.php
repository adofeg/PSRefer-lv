<?php

namespace App\Actions\Referrals;

use App\Data\Referrals\ReferralData;
use App\Http\Requests\Admin\ReferralRequest;
use App\Models\Offering;
use App\Services\FormSchemaValidator;

class SubmitReferralAction
{
    public function __construct(
        protected CreateReferralAction $createReferralAction,
        protected FormSchemaValidator $validator
    ) {}

    public function execute(ReferralData $data, array $formData = []): string
    {
        $offering = Offering::findOrFail($data->offering_id);

        $validatedFormData = [];
        if ($offering->form_schema && ! empty($offering->form_schema)) {
            $validatedFormData = $this->validator->validate(
                $offering->form_schema,
                $formData
            );
        }

        $allMetadata = array_merge($data->metadata ?? [], $validatedFormData);

        $selectedServices = $allMetadata['Servicios de Interés'] ?? null;
        if ($offering->name === 'Referencia General' && is_array($selectedServices) && ! empty($selectedServices)) {
            $offerings = Offering::whereIn('name', $selectedServices)->get();
            $count = 0;

            foreach ($offerings as $targetOffering) {
                $referralData = new ReferralData(
                    client_name: $data->client_name,
                    client_contact: $data->client_contact,
                    offering_id: $targetOffering->id,
                    metadata: array_merge($allMetadata, ['origen' => 'Referencia General']),
                    notes: '[Ref. General] '.($data->notes ?? ''),
                    associate_id: $data->associate_id
                );

                $this->createReferralAction->execute($referralData);
                $count++;
            }

            return "$count referidos creados exitosamente.";
        }

        $referralData = new ReferralData(
            client_name: $data->client_name,
            client_contact: $data->client_contact,
            offering_id: $data->offering_id,
            metadata: $allMetadata,
            notes: $data->notes,
            associate_id: $data->associate_id
        );

        $this->createReferralAction->execute($referralData);

        return 'Referral created successfully.';
    }
}
