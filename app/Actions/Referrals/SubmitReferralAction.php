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

    public function execute(ReferralRequest $request, int $associateId): string
    {
        $offering = Offering::findOrFail($request->validated('offering_id'));

        $formData = [];
        if ($offering->form_schema && ! empty($offering->form_schema)) {
            $formData = $this->validator->validate(
                $offering->form_schema,
                $request->input('form_data', [])
            );
        }

        $allMetadata = array_merge($request->validated('metadata') ?? [], $formData);

        $selectedServices = $allMetadata['Servicios de Interés'] ?? null;
        if ($offering->name === 'Referencia General' && is_array($selectedServices) && ! empty($selectedServices)) {
            $offerings = Offering::whereIn('name', $selectedServices)->get();
            $count = 0;

            foreach ($offerings as $targetOffering) {
                $referralData = new ReferralData(
                    client_name: $request->validated('client_name'),
                    client_contact: $request->validated('client_contact'),
                    offering_id: $targetOffering->id,
                    metadata: array_merge($allMetadata, ['origen' => 'Referencia General']),
                    notes: '[Ref. General] '.($request->validated('notes') ?? ''),
                    associate_id: $associateId
                );

                $this->createReferralAction->execute($referralData);
                $count++;
            }

            return "$count referidos creados exitosamente.";
        }

        $referralData = new ReferralData(
            client_name: $request->validated('client_name'),
            client_contact: $request->validated('client_contact'),
            offering_id: (int) $request->validated('offering_id'),
            metadata: $allMetadata,
            notes: $request->validated('notes'),
            associate_id: $associateId
        );

        $this->createReferralAction->execute($referralData);

        return 'Referral created successfully.';
    }
}
