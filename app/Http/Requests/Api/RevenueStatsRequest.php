<?php

namespace App\Http\Requests\Api;

use App\Data\Analytics\RevenueStatsQueryData;
use App\Enums\RoleName;
use Illuminate\Foundation\Http\FormRequest;

class RevenueStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        if ($user->hasRole([RoleName::Admin->value, RoleName::PsAdmin->value])) {
            return true;
        }

        if ($user->hasRole(RoleName::Associate->value)) {
            $associateId = $user->associateProfile()?->id;
            $requestedId = $this->input('associate_id');

            return !$requestedId || (int) $requestedId === (int) $associateId;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'associate_id' => 'nullable|integer|exists:associates,id',
        ];
    }

    public function toData(): RevenueStatsQueryData
    {
        return new RevenueStatsQueryData(
            associate_id: $this->validated('associate_id')
        );
    }
}
