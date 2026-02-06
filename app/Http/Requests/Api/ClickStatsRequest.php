<?php

namespace App\Http\Requests\Api;

use App\Enums\RoleName;
use Illuminate\Foundation\Http\FormRequest;

class ClickStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        return $user->hasRole([
            RoleName::Admin->value,
            RoleName::PsAdmin->value,
            RoleName::Associate->value,
        ]);
    }

    public function rules(): array
    {
        return [];
    }
}