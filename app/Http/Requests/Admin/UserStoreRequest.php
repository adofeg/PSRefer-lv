<?php

namespace App\Http\Requests\Admin;

use App\Data\Auth\UserData;
use App\Enums\RoleName;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:50',
            'role' => ['nullable', 'string', Rule::in(RoleName::adminOrAssociate())],
            'category' => 'nullable|string|max:255',
            'referrer_id' => 'nullable|integer|exists:associates,id',
            'offering_id' => 'nullable|integer|exists:offerings,id',
        ];
    }

    public function toData(): UserData
    {
        return new UserData(
            name: $this->validated('name'),
            email: $this->validated('email'),
            password: $this->validated('password'),
            phone: $this->validated('phone'),
            role: $this->validated('role') ?? RoleName::Associate->value,
            category: $this->validated('category'),
            referrer_id: $this->validated('referrer_id'),
            offering_id: $this->validated('offering_id')
        );
    }
}