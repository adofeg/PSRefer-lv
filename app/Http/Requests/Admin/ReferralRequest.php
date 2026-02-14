<?php

namespace App\Http\Requests\Admin;

use App\Data\Referrals\ReferralData;
use App\Data\Referrals\ReferralStatusUpdateData;
use App\Enums\RoleName;
use App\Enums\ReferralStatus;
use App\Models\Referral;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReferralRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->routeIs('admin.referrals.index') || $this->routeIs('admin.referrals.pipeline')) {
            return $this->user() !== null;
        }

        $referral = $this->route('referral');

        if ($referral) {
            return $this->user()->can('update', $referral);
        }

        return $this->user()->can('create', Referral::class);
    }

    public function rules(): array
    {
        if ($this->routeIs('admin.referrals.index') || $this->routeIs('admin.referrals.pipeline')) {
            return [
                'search' => ['nullable', 'string', 'max:120'],
                'status' => ['nullable', 'string', 'max:50'],
            ];
        }

        if ($this->routeIs('admin.referrals.create') && $this->isMethod('GET')) {
            return [
                'offering_id' => 'nullable|integer|exists:offerings,id',
            ];
        }

        $rules = [
            'client_name' => 'required|string|max:255',
            'client_contact' => 'required|string|max:255',
            'offering_id' => 'required|integer|exists:offerings,id',
            'metadata' => 'nullable|array',
            'notes' => 'nullable|string',
            'associate_id' => [
                RoleName::isAdmin($this->user()) ? 'required' : 'nullable',
                'integer',
                'exists:associates,id'
            ],
        ];

        // Update context
        if ($this->route('referral')) {
            // For updates, some fields might be readonly or optional depending on logic,
            // but standard consolidation keeps validation strict or 'sometimes'.
            // In the original UpdateReferralRequest, 'status' was validated.
            // Original StoreRequest did NOT validate 'status' (default).
            
            $rules = [
                'status' => ['sometimes', Rule::enum(ReferralStatus::class)],
                'deal_value' => 'nullable|numeric|min:0',
                'revenue_generated' => 'nullable|numeric|min:0',
                'contract_id' => 'nullable|string|max:255',
                'payment_method' => 'nullable|string|max:255',
                'down_payment' => 'nullable|numeric|min:0|lte:deal_value',
                'agency_fee' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                // Client data usually editable too? If so, merge.
                // But specifically for Referral Status Updates, often fields change.
                // Let's keep it comprehensive.
            ];

            $user = $this->user();
            if ($user && $user->hasRole(RoleName::Associate->value)) {
                $rules['status'] = ['prohibited'];
                $rules['deal_value'] = ['prohibited'];
                $rules['revenue_generated'] = ['prohibited'];
                $rules['contract_id'] = ['prohibited'];
                $rules['payment_method'] = ['prohibited'];
                $rules['down_payment'] = ['prohibited'];
                $rules['agency_fee'] = ['prohibited'];
            }
        }

        return $rules;
    }

    public function toStoreData(int $associateId): ReferralData
    {
        return new ReferralData(
            client_name: $this->validated('client_name'),
            client_contact: $this->validated('client_contact'),
            offering_id: (int) $this->validated('offering_id'),
            status: ReferralStatus::Prospect,
            metadata: $this->validated('metadata') ?? [],
            notes: $this->validated('notes'),
            associate_id: $associateId
        );
    }

    public function toStatusUpdateData(ReferralStatus $currentStatus): ReferralStatusUpdateData
    {
        $status = $this->validated('status')
            ? ReferralStatus::from($this->validated('status'))
            : $currentStatus;

        return new ReferralStatusUpdateData(
            status: $status,
            deal_value: $this->validated('deal_value'),
            revenue_generated: $this->validated('revenue_generated'),
            contract_id: $this->validated('contract_id'),
            payment_method: $this->validated('payment_method'),
            down_payment: $this->validated('down_payment'),
            agency_fee: $this->validated('agency_fee'),
            notes: $this->validated('notes')
        );
    }
}
