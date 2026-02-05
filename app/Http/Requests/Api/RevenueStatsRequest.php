<?php

namespace App\Http\Requests\Api;

use App\Data\Analytics\RevenueStatsQueryData;
use Illuminate\Foundation\Http\FormRequest;

class RevenueStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
