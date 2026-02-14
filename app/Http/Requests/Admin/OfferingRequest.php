<?php

namespace App\Http\Requests\Admin;

use App\Data\Offerings\OfferingUpsertData;
use App\Models\Offering;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OfferingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        if ($this->routeIs('admin.offerings.index')) {
            return true;
        }

        if ($this->routeIs('admin.offerings.toggle-status')) {
            $offering = $this->route('offering');

            return $offering !== null && $user->can('update', $offering);
        }

        $offering = $this->route('offering');

        if ($offering) {
            return $user->can('update', $offering);
        }

        return $user->can('create', Offering::class);
    }

    public function rules(): array
    {
        if ($this->routeIs('admin.offerings.index')) {
            return [
                'search' => ['nullable', 'string', 'max:120'],
                'category' => ['nullable', 'integer', 'exists:categories,id'],
                'status' => ['nullable', Rule::in(['all', 'true', 'false', '1', '0'])],
                'json' => ['nullable', 'boolean'],
            ];
        }

        if ($this->routeIs('admin.offerings.toggle-status')) {
            return [
                'is_active' => ['required', 'boolean'],
            ];
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(['service', 'product', 'professional'])],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'category' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'base_price' => ['nullable', 'numeric'],
            'commission_rate' => ['nullable', 'numeric'],
            'form_schema' => ['nullable', 'array'],
            'commission_config' => ['nullable', 'array'],
            'commission_rules' => ['nullable', 'array'],
            'notification_emails' => ['nullable', 'array'],
            'notification_emails.*' => ['email'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['name'] = ['sometimes', 'string', 'max:255'];
            $rules['is_active'] = ['sometimes', 'boolean'];
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
