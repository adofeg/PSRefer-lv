<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Offering;

class OfferingRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Polymorphic Policy Check
        // If route has 'offering' parameter, it's an update/delete -> check 'update' policy
        // If not, it's a store -> check 'create' policy
        $offering = $this->route('offering');

        if ($offering) {
            return $this->user()->can('update', $offering);
        }

        return $this->user()->can('create', Offering::class);
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price' => 'nullable|numeric',
            'commission_rate' => 'nullable|numeric',
            'form_schema' => 'nullable|array',
            'commission_config' => 'nullable|array',
        ];

        // Add update-specific rules
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'sometimes|string|max:255'; // sometimes if partial update
            $rules['is_active'] = 'sometimes|boolean';
        }

        return $rules;
    }
}
