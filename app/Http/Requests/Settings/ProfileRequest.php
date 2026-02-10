<?php

namespace App\Http\Requests\Settings;

use App\Data\Settings\UserSettingsData;
use App\Enums\CurrencyCode;
use App\Enums\W9Status;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'w9_status' => ['required', Rule::enum(W9Status::class)],
            'payment_info' => 'nullable|array',
            'preferred_currency' => ['required', Rule::enum(CurrencyCode::class)],
            'category' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'logo_file' => 'nullable|image|max:2048',
            'w9_file' => 'nullable|file|mimes:pdf,jpg,png,webp|max:5120',
        ];
    }

    public function toData(): UserSettingsData
    {
        return new UserSettingsData(
            name: $this->validated('name'),
            w9_status: W9Status::from($this->validated('w9_status')),
            payment_info: $this->validated('payment_info') ?? [],
            preferred_currency: CurrencyCode::from($this->validated('preferred_currency')),
            category: $this->validated('category'),
            phone: $this->validated('phone'),
            logo_file: $this->file('logo_file'),
            w9_file: $this->file('w9_file')
        );
    }

    public function validateForDelete(): void
    {
        $this->validate([
            'current_password' => 'required|current_password',
        ]);
    }
}
