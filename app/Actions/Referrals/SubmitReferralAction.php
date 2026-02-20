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
        protected \App\Services\OfferingSchemaService $schemaService,
        protected \App\Actions\Files\CreateFileAssetAction $createFileAssetAction
    ) {}

    public function execute(ReferralData $data, array $formData = []): string
    {
        $offering = Offering::findOrFail($data->offering_id);
        $schema = $this->schemaService->getSchemaForOffering($offering->form_schema);

        if (! empty($schema)) {
            // Flatten schema for validator (it expects list of fields)
            $flatSchema = $this->schemaService->flattenFields($schema);
            
            // Validate to ensure schema compliance, but don't limit to schema fields
            $this->validator->validate(
                $flatSchema,
                $formData
            );
        }

        // Use all formData, including orphans not in schema
        $allMetadata = array_merge($data->metadata ?? [], $formData);
        
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
                        'client_email' => $extracted['client_email'] ?? null,
                        'client_phone' => $extracted['client_phone'] ?? null,
                    ]),
                    notes: '[Ref. General] '.($data->notes ?? ''),
                    consent_confirmed: $data->consent_confirmed,
                    associate_id: $data->associate_id
                );

                $this->createReferralAction->execute($referralData);
                $count++;
            }

            return "$count referidos creados exitosamente.";
        }

        $files = [];
        $scalars = [];

        foreach ($allMetadata as $key => $value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $files[$key] = $value;
                // Store placeholder in metadata or remove? Removing better for JSON size.
                $scalars[$key] = null; 
            } else {
                $scalars[$key] = $value;
            }
        }

        $referralData = new ReferralData(
            offering_id: $data->offering_id,
            metadata: $scalars,
            notes: $data->notes,
            consent_confirmed: $data->consent_confirmed,
            associate_id: $data->associate_id,
            client_name: $data->client_name,
            client_email: $data->client_email,
            client_phone: $data->client_phone,
        );

        $referral = $this->createReferralAction->execute($referralData);

        // Process Files
        foreach ($files as $field => $file) {
            $path = $file->store('referrals/' . $referral->uuid, 'public');
            
            $asset = $this->createFileAssetAction->execute(
                new \App\Data\Files\FileAssetData(
                    disk: 'public',
                    path: $path,
                    original_name: $file->getClientOriginalName(),
                    mime_type: $file->getMimeType(),
                    size: $file->getSize(),
                    purpose: $field, // Use field name as purpose to map back
                    category: 'document',
                    uploaded_by: auth()->id(),
                    attachable_type: get_class($referral),
                    attachable_id: $referral->id
                )
            );

            // Update metadata reference
            $scalars[$field] = $asset->uuid;
        }

        if (!empty($files)) {
            $referral->update(['metadata' => $scalars]);
        }

        return 'Referral created successfully.';
    }
}
