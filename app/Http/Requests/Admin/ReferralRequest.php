<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Referral;

class ReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        $referral = $this->route('referral');

        if ($referral) {
            return $this->user()->can('update', $referral);
        }

        return $this->user()->can('create', Referral::class);
    }

    public function rules(): array
    {
        $rules = [
            'client_name' => 'required|string|max:255',
            'client_contact' => 'required|string|max:255',
            'offering_id' => 'required|uuid|exists:offerings,id',
            'metadata' => 'nullable|array',
            'notes' => 'nullable|string',
        ];

        // Update context
        if ($this->route('referral')) {
            // For updates, some fields might be readonly or optional depending on logic,
            // but standard consolidation keeps validation strict or 'sometimes'.
            // In the original UpdateReferralRequest, 'status' was validated.
            // Original StoreRequest did NOT validate 'status' (default).
            
            $rules = [
                'status' => 'sometimes|string',
                'deal_value' => 'nullable|numeric',
                'revenue_generated' => 'nullable|numeric',
                'notes' => 'nullable|string',
                // Client data usually editable too? If so, merge.
                // But specifically for Referral Status Updates, often fields change.
                // Let's keep it comprehensive.
            ];
        }

        return $rules;
    }
}
