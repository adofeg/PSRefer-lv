<?php

namespace App\Http\Requests\Admin;

use App\Data\Offerings\OfferingUpsertData;
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
            'type' => 'required|string|in:service,product,professional',
            'category_id' => 'nullable|integer|exists:categories,id',
            'category' => 'nullable|string|max:1000',
            'description' => 'nullable|string',
            'base_price' => 'nullable|numeric',
            'commission_rate' => 'nullable|numeric',
            'form_schema' => 'nullable|array',
            'commission_config' => 'nullable|array',
            'commission_rules' => 'nullable|array',
            'notification_emails' => 'nullable|array',
            'notification_emails.*' => 'email',
        ];

        // Add update-specific rules
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = 'sometimes|string|max:255'; // sometimes if partial update
            $rules['is_active'] = 'sometimes|boolean';
        }

        return $rules;
    }

    public function toData(): OfferingUpsertData
    {
        return new OfferingUpsertData(
            name: $this->validated('name', $this->route('offering')?->name),
            category_id: $this->validated('category_id'),
            category: $this->validated('category'),
            type: $this->validated('type'),
            description: $this->validated('description'),
            base_price: $this->validated('base_price'),
            commission_rate: $this->validated('commission_rate'),
            form_schema: $this->validated('form_schema'),
            commission_config: $this->validated('commission_config'),
            commission_rules: $this->validated('commission_rules'),
            notification_emails: $this->validated('notification_emails'),
            is_active: $this->validated('is_active')
        );
    }
}
