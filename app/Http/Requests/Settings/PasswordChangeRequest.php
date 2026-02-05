<?php

namespace App\Http\Requests\Settings;

use App\Data\Settings\PasswordChangeData;
use Illuminate\Foundation\Http\FormRequest;

class PasswordChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8|different:current_password',
        ];
    }

    public function toData(): PasswordChangeData
    {
        return new PasswordChangeData(
            current_password: $this->validated('current_password'),
            new_password: $this->validated('new_password')
        );
    }
}
