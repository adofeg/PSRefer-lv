<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\CommissionStatus;

class CommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Admin middleware handles checking
    }

    public function rules(): array
    {
        return [
            'associate_id' => 'required|integer|exists:associates,id',
            'referral_id' => 'nullable|integer|exists:referrals,id',
            'amount' => 'required|numeric|min:0.01',
            'commission_type' => 'required|string|max:50',
            'status' => ['required', Rule::enum(CommissionStatus::class)],
            'notes' => 'nullable|string|max:500',
        ];
    }
}
