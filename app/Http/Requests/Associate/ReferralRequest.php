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
            'form_data' => ['required', 'array'],
        ];
    }

    public function toStoreData(int $associateId): \App\Data\Referrals\ReferralData
    {
        return new \App\Data\Referrals\ReferralData(
            offering_id: (int) $this->validated('offering_id'),
            status: \App\Enums\ReferralStatus::Prospect,
            metadata: [],
            notes: $this->validated('notes'),
            associate_id: $associateId
        );
    }
}
