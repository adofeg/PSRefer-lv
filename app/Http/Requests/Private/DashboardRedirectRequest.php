<?php

namespace App\Http\Requests\Private;

use Illuminate\Foundation\Http\FormRequest;

class DashboardRedirectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
    }
}
