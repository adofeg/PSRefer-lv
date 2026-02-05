<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Laravel\Fortify\Features;

class TwoFactorAuthenticationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function ensureStateIsValid(): void
    {
        if (! Features::enabled(Features::twoFactorAuthentication())) {
            abort(404);
        }
    }
}
