<?php

namespace App\Http\Requests\Admin;

use App\Data\Auth\UserData;
use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $actor = $this->user();
        if (!$actor) {
            return false;
        }

        if ($this->routeIs('admin.users.store')) {
            return $actor->can('create', User::class);
        }

        if ($this->routeIs('admin.users.update') || $this->routeIs('admin.users.toggle-status')) {
            $targetUser = $this->route('user');

            return $targetUser !== null && $actor->can('update', $targetUser);
        }

        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('admin.users.index')) {
            return [
                'search' => ['nullable', 'string', 'max:120'],
                'role' => ['nullable', 'string', Rule::in(array_column(RoleName::cases(), 'value'))],
                'status' => ['nullable', Rule::in(['all', 'true', 'false', '1', '0'])],
            ];
        }

        if ($this->routeIs('admin.users.toggle-status')) {
            return [
                'is_active' => ['required', 'boolean'],
            ];
        }

        if ($this->routeIs('admin.users.store')) {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
                'phone' => ['nullable', 'string', 'max:50'],
                'role' => ['nullable', 'string', Rule::in(RoleName::adminOrAssociate())],
                'category' => ['nullable', 'string', 'max:255'],
                'referrer_id' => ['nullable', 'integer', 'exists:associates,id'],
                'offering_id' => ['nullable', 'integer', 'exists:offerings,id'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user')),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['required', 'string', Rule::in(RoleName::adminOrAssociate())],
            'category' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
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
