<?php

namespace App\Http\Requests\Public;

use App\Data\Public\OfferingApplicationData;
use Illuminate\Foundation\Http\FormRequest;

class LeadSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'offering_id' => 'required|integer|exists:offerings,id',
            'referrer_id' => 'required|integer|exists:associates,id',
            'client_name' => 'required|string|max:255',
            'client_contact' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'form_data' => 'nullable|array',
            'metadata' => 'nullable|array',
        ];
    }

    public function toData(): OfferingApplicationData
    {
        $formData = $this->validated('form_data') ?? [];
        $metadata = $this->validated('metadata') ?? [];

        return new OfferingApplicationData(
            client_name: $this->validated('client_name'),
            client_contact: $this->validated('client_contact'),
            referrer_id: (int) $this->validated('referrer_id'),
            form_data: array_merge($formData, $metadata),
            notes: $this->validated('notes')
        );
    }
}