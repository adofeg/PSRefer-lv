<?php

namespace App\Http\Requests\Api;

use App\Data\Analytics\RevenueStatsQueryData;
use App\Enums\RoleName;
use Illuminate\Foundation\Http\FormRequest;

class AnalyticsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $canAccessAnalytics = $user->hasRole([
            RoleName::Admin->value,
            RoleName::PsAdmin->value,
            RoleName::Associate->value,
        ]);

        if (! $canAccessAnalytics) {
            return false;
        }

        if ($this->routeIs('api.analytics.revenue') && $user->hasRole(RoleName::Associate->value)) {
            $associateId = $user->associateProfile()?->id;
            $requestedId = $this->input('associate_id');

            return ! $requestedId || (int) $requestedId === (int) $associateId;
        }

        return true;
    }

    public function rules(): array
    {
        if ($this->routeIs('api.analytics.revenue')) {
            return [
                'associate_id' => ['nullable', 'integer', 'exists:associates,id'],
            ];
        }

        return [];
    }

    public function toRevenueData(): RevenueStatsQueryData
    {
        return new RevenueStatsQueryData(
            associate_id: $this->validated('associate_id')
        );
    }
}
