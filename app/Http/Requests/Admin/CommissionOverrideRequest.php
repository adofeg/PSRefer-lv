<?php

namespace App\Http\Requests\Admin;

use App\Data\Commissions\CommissionOverrideQueryData;
use App\Data\Commissions\CommissionOverrideUpsertData;
use App\Models\CommissionOverride;
use Illuminate\Foundation\Http\FormRequest;

class CommissionOverrideRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->isMethod('POST')) {
            return $this->user()?->can('create', CommissionOverride::class) ?? false;
        }

        return $this->user()?->can('viewAny', CommissionOverride::class) ?? false;
    }

    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'associate_id' => 'required|integer|exists:associates,id',
                'offering_id' => 'nullable|integer|exists:offerings,id',
                'base_commission' => 'required|numeric|min:0|max:100',
            ];
        }

        return [
            'associate_id' => 'nullable|integer',
        ];
    }

    public function toQueryData(): CommissionOverrideQueryData
    {
        return new CommissionOverrideQueryData(
            associate_id: $this->validated('associate_id') ? (int) $this->validated('associate_id') : 0
        );
    }

    public function toUpsertData(): CommissionOverrideUpsertData
    {
        return new CommissionOverrideUpsertData(
            associate_id: (int) $this->validated('associate_id'),
            offering_id: $this->validated('offering_id') ? (int) $this->validated('offering_id') : null,
            base_commission: (float) $this->validated('base_commission')
        );
    }
}
