<?php

namespace App\Http\Requests\Admin;

use App\Data\Settings\SmtpSettingsData;
use App\Enums\RoleName;
use Illuminate\Foundation\Http\FormRequest;

class SmtpSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole(RoleName::Admin->value) ?? false;
    }

    public function rules(): array
    {
        if ($this->routeIs('admin.settings.smtp') || $this->routeIs('admin.settings.smtp.test')) {
            return [];
        }

        return [
            'host' => ['required', 'string'],
            'port' => ['required', 'string'],
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'encryption' => ['required', 'string'],
            'from_address' => ['required', 'email'],
            'from_name' => ['required', 'string'],
        ];
    }

    public function toData(): SmtpSettingsData
    {
        return new SmtpSettingsData(
            host: $this->validated('host'),
            port: $this->validated('port'),
            username: $this->validated('username'),
            password: $this->validated('password'),
            encryption: $this->validated('encryption'),
            from_address: $this->validated('from_address'),
            from_name: $this->validated('from_name')
        );
    }
}
