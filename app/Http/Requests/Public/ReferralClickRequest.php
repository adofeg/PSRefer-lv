<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class ReferralClickRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'referrer_id' => 'required|integer|exists:associates,id',
            'offering_id' => 'nullable|integer|exists:offerings,id',
            'link_type' => 'nullable|string|max:50',
        ];
    }

    public function toData(): array
    {
        return [
            'referrer_id' => (int) $this->validated('referrer_id'),
            'offering_id' => $this->validated('offering_id') !== null ? (int) $this->validated('offering_id') : null,
            'link_type' => $this->validated('link_type') ?? 'general',
        ];
    }
}