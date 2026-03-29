<?php

namespace App\Http\Requests\Associate;

use Illuminate\Foundation\Http\FormRequest;

class ReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->associate !== null;
    }

    public function rules(): array
    {
        if ($this->routeIs('associate.referrals.index')) {
            return [
                'search' => ['nullable', 'string', 'max:120'],
            ];
        }

        if ($this->routeIs('associate.referrals.create') && $this->isMethod('GET')) {
            return [
                'offering_id' => ['nullable', 'integer', 'exists:offerings,id'],
            ];
        }

        return [
            'offering_id' => ['required', 'integer', 'exists:offerings,id'],
            'notes' => ['nullable', 'string'],
            'consent_confirmed' => ['boolean'],
            'form_data' => ['required', 'array'],
            'sector_id' => ['required', 'integer', 'exists:sectors,id'],
        ];
    }

    /**
     * Map request to ReferralData object.
     * Core client fields are extracted by the Action and stored in metadata.
     */
    public function toStoreData(?int $associateId): \App\Data\Referrals\ReferralData
    {
        return new \App\Data\Referrals\ReferralData(
            offering_id: (int) $this->validated('offering_id'),
            status: \App\Enums\ReferralStatus::Prospect,
            metadata: [],
            notes: $this->validated('notes'),
            consent_confirmed: (bool) $this->validated('consent_confirmed'),
            associate_id: $associateId,
            sector_id: (int) $this->validated('sector_id'),
        );
    }
}
