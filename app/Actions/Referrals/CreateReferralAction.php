<?php

namespace App\Actions\Referrals;

use App\Data\Referrals\ReferralData;
use App\Enums\ReferralStatus;
use App\Enums\RoleName;
use App\Mail\NewReferralAlertMail;
use App\Models\Associate;
use App\Models\Offering;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CreateReferralAction
{
    public function execute(ReferralData $data): Referral
    {
        $offering = Offering::with('category')->findOrFail($data->offering_id);
        $associate = $data->associate_id ? Associate::find($data->associate_id) : null;
        $user = $associate?->user;

        // CONFLICT OF INTEREST VALIDATION (Parity with JS)
        // Associates cannot refer services in their own category
        if ($user && $user->hasRole(RoleName::Associate->value) && $associate->category && $offering->category) {
            if (strtolower($associate->category) === strtolower($offering->category->name)) {
                throw ValidationException::withMessages([
                    'offering_id' => ["Conflicto de intereses: No puedes referir servicios de tu misma categoría ({$associate->category})."],
                ]);
            }
        }

        $referral = DB::transaction(function () use ($data) {
            $metadata = $data->metadata ?? [];
            
            // Ensure core fields are explicitly set in metadata for accessors
            if ($data->client_name) $metadata['client_name'] = $data->client_name;
            if ($data->client_email) $metadata['client_email'] = $data->client_email;
            if ($data->client_phone) $metadata['client_phone'] = $data->client_phone;

            return Referral::create([
                'associate_id' => $data->associate_id,
                'offering_id' => $data->offering_id,
                'status' => $data->status?->value ?? ReferralStatus::Prospect->value,
                'metadata' => $metadata,
                'notes' => $data->notes,
                'consent_confirmed' => $data->consent_confirmed,
            ]);
        });

        // Notify Admins
        try {
            $admins = User::role(RoleName::adminRoles())->get();
            if ($admins->isNotEmpty()) {
                Mail::to($admins)->send(new NewReferralAlertMail($referral, $user ?? auth()->user()));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send new referral alert: '.$e->getMessage());
        }

        return $referral;
    }
}
