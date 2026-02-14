<?php

namespace App\Http\Requests\Public;

use App\Data\Public\OfferingApplicationData;
use Illuminate\Foundation\Http\FormRequest;

class PublicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('site.apply') || $this->routeIs('site.invite')) {
            return [
                'ref' => ['nullable', 'integer', 'exists:associates,id'],
            ];
        }

        if ($this->routeIs('site.apply.submit')) {
            return [
                'referrer_id' => ['nullable', 'integer', 'exists:associates,id'],
                'form_data' => ['required', 'array'],
                'notes' => ['nullable', 'string', 'max:1000'],
            ];
        }

        return [];
    }

    public function toData(): OfferingApplicationData
    {
        return new OfferingApplicationData(
            referrer_id: $this->validated('referrer_id'),
            form_data: $this->validated('form_data') ?? [],
            notes: $this->validated('notes')
        );
    }
}
