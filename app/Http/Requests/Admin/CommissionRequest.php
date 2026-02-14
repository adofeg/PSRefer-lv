<?php

namespace App\Http\Requests\Admin;

use App\Enums\CommissionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        if ($this->routeIs('admin.commissions.index')) {
            return [
                'search' => ['nullable', 'string', 'max:120'],
                'status' => ['nullable', Rule::in(array_merge(['all'], array_column(CommissionStatus::cases(), 'value')))],
                'associate_id' => ['nullable', 'integer', 'exists:associates,id'],
            ];
        }

        return [
            'associate_id' => ['required', 'integer', 'exists:associates,id'],
            'referral_id' => ['nullable', 'integer', 'exists:referrals,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'commission_type' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::enum(CommissionStatus::class)],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
