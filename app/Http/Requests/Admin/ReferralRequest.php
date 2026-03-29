<?php

namespace App\Http\Requests\Admin;

use App\Data\Referrals\ReferralData;
use App\Data\Referrals\ReferralStatusUpdateData;
use App\Enums\AssociateRole;
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
                'offering_id' => ['nullable', 'integer', 'exists:offerings,id'],
                'associate_id' => ['nullable', 'integer', 'exists:associates,id'],
                'sector_id' => ['nullable', 'integer', 'exists:sectors,id'],
            ];
        }

        if ($this->routeIs('admin.referrals.create') && $this->isMethod('GET')) {
            return [
                'offering_id' => 'nullable|integer|exists:offerings,id',
            ];
        }

        $rules = [
            'offering_id' => 'required|integer|exists:offerings,id',
            'form_data' => 'required|array',
            'metadata' => 'nullable|array',
            'notes' => 'nullable|string',
            'consent_confirmed' => 'boolean',
            'associate_id' => [
                'nullable',
                'integer',
                'exists:associates,id',
            ],
            'sector_id' => 'required|integer|exists:sectors,id',
        ];

        // Update context
        if ($this->route('referral')) {
            // For updates, some fields might be readonly or optional depending on logic,
            // but standard consolidation keeps validation strict or 'sometimes'.
            // In the original UpdateReferralRequest, 'status' was validated.
            // Original StoreRequest did NOT validate 'status' (default).

            $rules = [
                'status' => ['sometimes', Rule::enum(ReferralStatus::class)],
                'contract_id' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'reminder_date' => 'nullable|date',
            ];

            $user = $this->user();
            if ($user && $user->hasRole(AssociateRole::ASSOCIATE->value)) {
                $rules['status'] = ['prohibited'];
                $rules['contract_id'] = ['prohibited'];
            }
        }

        return $rules;
    }

    public function toStoreData(?int $associateId): ReferralData
    {
        return new ReferralData(
            offering_id: (int) $this->validated('offering_id'),
            status: ReferralStatus::Prospect,
            metadata: $this->validated('metadata') ?? [],
            notes: $this->validated('notes'),
            consent_confirmed: (bool) $this->validated('consent_confirmed'),
            associate_id: $associateId,
            sector_id: (int) $this->validated('sector_id'),
        );
    }

    public function toStatusUpdateData(ReferralStatus $currentStatus): ReferralStatusUpdateData
    {
        $status = $this->validated('status')
            ? ReferralStatus::from($this->validated('status'))
            : $currentStatus;

        return new ReferralStatusUpdateData(
            status: $status,
            contract_id: $this->validated('contract_id'),
            notes: $this->validated('notes'),
            reminder_date: $this->validated('reminder_date')
        );
    }
}
