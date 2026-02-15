<?php

namespace App\Actions\Referrals;

use App\Data\Referrals\ReferralData;
use App\Models\Offering;
use App\Services\FormSchemaValidator;

class SubmitReferralAction
{
    public function __construct(
        protected CreateReferralAction $createReferralAction,
        protected FormSchemaValidator $validator,
        protected \App\Services\OfferingSchemaService $schemaService
    ) {}

    public function execute(ReferralData $data, array $formData = []): string
    {
        $offering = Offering::findOrFail($data->offering_id);
        $schema = $this->schemaService->getSchemaForOffering($offering->form_schema);

        $validatedFormData = [];
        if (! empty($schema)) {
            $validatedFormData = $this->validator->validate(
                $schema,
                $formData
            );
        }

        $allMetadata = array_merge($data->metadata ?? [], $validatedFormData);
        
        // Inject Schema Version for historical tracking
        if (isset($schema['version'])) {
            $allMetadata['_schema_version'] = $schema['version'];
        }        
        // Shadow Sync: Extract core fields from dynamic data
        $extracted = $this->schemaService->extractCoreFields($schema, $allMetadata);

        $selectedServices = $allMetadata['Servicios de Interés'] ?? null;
        if ($offering->name === 'Referencia General' && is_array($selectedServices) && ! empty($selectedServices)) {
            $offerings = Offering::whereIn('name', $selectedServices)->get();
            $count = 0;

            foreach ($offerings as $targetOffering) {
                $referralData = new ReferralData(
                    offering_id: $targetOffering->id,
                    metadata: array_merge($allMetadata, [
                        'origen' => 'Referencia General',
                        'client_name' => $extracted['client_name'] ?? null,
                        'client_contact' => $extracted['client_contact'] ?? null,
                        'client_state' => $extracted['client_state'] ?? null
                    ]),
                    notes: '[Ref. General] '.($data->notes ?? ''),
                    associate_id: $data->associate_id
                );

                $this->createReferralAction->execute($referralData);
                $count++;
            }

            return "$count referidos creados exitosamente.";
        }

        $referralData = new ReferralData(
            offering_id: $data->offering_id,
            metadata: $allMetadata,
            notes: $data->notes,
            associate_id: $data->associate_id,
            client_name: $data->client_name,
            client_email: $data->client_email,
            client_phone: $data->client_phone,
        );

        $this->createReferralAction->execute($referralData);

        return 'Referral created successfully.';
    }
}
