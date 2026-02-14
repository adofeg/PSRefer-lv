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
            'client_name' => ['required', 'string', 'max:255'],
            'client_email' => ['required', 'email', 'max:255'],
            'client_phone' => ['required', 'string', 'max:20'],
            'client_state' => ['required', 'string', 'max:50'],
            'offering_id' => ['required', 'integer', 'exists:offerings,id'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function toStoreData(int $associateId): \App\Data\Referrals\ReferralData
    {
        return new \App\Data\Referrals\ReferralData(
            client_name: $this->validated('client_name'),
            client_contact: $this->validated('client_email').' | '.$this->validated('client_phone'),
            offering_id: (int) $this->validated('offering_id'),
            status: \App\Enums\ReferralStatus::Prospect,
            metadata: ['client_state' => $this->validated('client_state')],
            notes: $this->validated('notes'),
            associate_id: $associateId
        );
    }
}
