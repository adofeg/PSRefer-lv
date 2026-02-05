<?php

namespace App\Http\Requests\Admin;

use App\Data\Commissions\CommissionOverrideQueryData;
use App\Data\Commissions\CommissionOverrideUpsertData;
use Illuminate\Foundation\Http\FormRequest;

class CommissionOverrideRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'associate_id' => 'required|integer|exists:associates,id',
                'offering_id' => 'required|integer|exists:offerings,id',
                'commission_rate' => 'required|numeric|min:0|max:100',
            ];
        }

        return [
            'associate_id' => 'required|integer',
        ];
    }

    public function toQueryData(): CommissionOverrideQueryData
    {
        return new CommissionOverrideQueryData(
            associate_id: (int) $this->validated('associate_id')
        );
    }

    public function toUpsertData(): CommissionOverrideUpsertData
    {
        return new CommissionOverrideUpsertData(
            associate_id: (int) $this->validated('associate_id'),
            offering_id: (int) $this->validated('offering_id'),
            commission_rate: (float) $this->validated('commission_rate')
        );
    }
}