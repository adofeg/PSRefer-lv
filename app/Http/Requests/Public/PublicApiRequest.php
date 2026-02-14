<?php

namespace App\Http\Requests\Public;

use App\Data\Public\OfferingApplicationData;
use Illuminate\Foundation\Http\FormRequest;

class PublicApiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('site.leads.store')) {
            return [
                'offering_id' => ['required', 'integer', 'exists:offerings,id'],
                'referrer_id' => ['required', 'integer', 'exists:associates,id'],
                'client_name' => ['required', 'string', 'max:255'],
                'client_contact' => ['required', 'string', 'max:255'],
                'notes' => ['nullable', 'string', 'max:1000'],
                'form_data' => ['nullable', 'array'],
                'metadata' => ['nullable', 'array'],
            ];
        }

        if ($this->routeIs('site.clicks.store')) {
            return [
                'referrer_id' => ['required', 'integer', 'exists:associates,id'],
                'offering_id' => ['nullable', 'integer', 'exists:offerings,id'],
                'link_type' => ['nullable', 'string', 'max:50'],
            ];
        }

        return [];
    }

    public function toLeadData(): OfferingApplicationData
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

    public function toClickData(): array
    {
        return [
            'referrer_id' => (int) $this->validated('referrer_id'),
            'offering_id' => $this->validated('offering_id') !== null ? (int) $this->validated('offering_id') : null,
            'link_type' => $this->validated('link_type') ?? 'general',
        ];
    }
}
