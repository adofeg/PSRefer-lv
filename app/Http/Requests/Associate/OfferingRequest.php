<?php

namespace App\Http\Requests\Associate;

use Illuminate\Foundation\Http\FormRequest;

class OfferingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:120'],
        ];
    }
}
