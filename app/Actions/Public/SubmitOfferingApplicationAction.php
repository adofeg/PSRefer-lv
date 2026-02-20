<?php

namespace App\Actions\Public;

use App\Data\Public\OfferingApplicationData;
use App\Enums\ReferralStatus;
use App\Models\Offering;
use App\Models\Referral;
use App\Services\FormSchemaValidator;
use App\Services\OfferingSchemaService;

class SubmitOfferingApplicationAction
{
    public function __construct(
        protected FormSchemaValidator $validator,
        protected OfferingSchemaService $schemaService,
        protected TrackReferralClickAction $trackReferralClickAction,
        protected \App\Actions\Files\CreateFileAssetAction $createFileAssetAction
    ) {}

    public function execute(
        int $offeringId,
        OfferingApplicationData $data,
        $request,
        string $source = 'public_landing',
        string $linkType = 'conversion'
    ): array {
        $createdReferrals = [];
        $offering = Offering::where('id', $offeringId)
            ->where('is_active', true)
            ->firstOrFail();

        $schema = $this->schemaService->getSchemaForOffering($offering->form_schema);

        $formData = $data->form_data ?? [];
        if (! empty($schema)) {
            // Flatten schema for validator
            $flatSchema = $this->schemaService->flattenFields($schema);

            // Validate but keep all data
            $validated = $this->validator->validate(
                $flatSchema,
                $formData
            );
            $formData = array_merge($formData, $validated);
        }

        $extracted = $this->schemaService->extractCoreFields($schema, $formData);

        // Separate Files from Scalars
        $files = [];
        $scalars = array_merge($formData, [
            'client_name' => $extracted['client_name'] ?? null,
            'client_email' => $extracted['client_email'] ?? null,
            'client_phone' => $extracted['client_phone'] ?? null,
            'client_state' => $extracted['client_state'] ?? null,
        ]);

        foreach ($scalars as $key => $value) {
            if ($value instanceof \Illuminate\Http\UploadedFile) {
                $files[$key] = $value;
                $scalars[$key] = null; // Placeholder for JSON
            }
        }

        $assignedAssociateId = $data->referrer_id ?? null;

        if ($offering->name === 'Referencia General' && isset($formData['Servicios de Interés']) && is_array($formData['Servicios de Interés'])) {
            $selectedServices = $formData['Servicios de Interés'];
            $targetOfferings = Offering::whereIn('name', $selectedServices)->get();

            foreach ($targetOfferings as $target) {
                $referral = Referral::create([
                    'associate_id' => $assignedAssociateId,
                    'offering_id' => $target->id,
                    'metadata' => array_merge(
                        ['source' => $source, 'origen' => 'Referencia General'],
                        $scalars
                    ),
                    'notes' => '[Ref. General] '.($data->notes ?? ''),
                    'status' => ReferralStatus::Prospect->value,
                ]);

                $this->attachFilesToReferral($referral, $files, $scalars);
                $createdReferrals[] = $referral;
            }

            if ($data->referrer_id) {
                $this->trackReferralClickAction->execute($data->referrer_id, $offeringId, $request, $linkType);
            }

            return $this->formatReferralsResponse($createdReferrals);
        }

        $referral = Referral::create([
            'associate_id' => $assignedAssociateId,
            'offering_id' => $offering->id,
            'metadata' => array_merge(
                ['source' => $source],
                $scalars
            ),
            'notes' => $data->notes,
            'status' => ReferralStatus::Prospect->value,
        ]);

        $this->attachFilesToReferral($referral, $files, $scalars);
        $createdReferrals[] = $referral;

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

    protected function attachFilesToReferral(Referral $referral, array $files, array $currentMetadata): void
    {
        if (empty($files)) {
            return;
        }

        $updatedMetadata = $currentMetadata;

        foreach ($files as $field => $file) {
            // Store unique copy per referral or shared path?
            // Storing unique path ensures isolation if one is deleted and file name uniqueness
            $path = $file->store('referrals/' . $referral->uuid, 'public');

            $asset = $this->createFileAssetAction->execute(
                new \App\Data\Files\FileAssetData(
                    disk: 'public',
                    path: $path,
                    original_name: $file->getClientOriginalName(),
                    mime_type: $file->getMimeType(),
                    size: $file->getSize(),
                    purpose: $field,
                    category: 'document',
                    uploaded_by: null, // Public submission
                    attachable_type: get_class($referral),
                    attachable_id: $referral->id
                )
            );

            $updatedMetadata[$field] = $asset->uuid;
        }

        $referral->update(['metadata' => $updatedMetadata]);
    }
}
