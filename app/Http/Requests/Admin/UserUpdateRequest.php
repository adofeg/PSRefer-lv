<?php

namespace App\Http\Requests\Admin;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('user')) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user')),
            ],
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:50',
            'role' => ['required', 'string', Rule::in(RoleName::cases())],
            'category' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }
}
