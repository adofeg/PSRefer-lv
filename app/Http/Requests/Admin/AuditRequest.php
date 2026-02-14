<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:120'],
            'action' => ['nullable', 'string', 'max:80'],
            'actor_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
