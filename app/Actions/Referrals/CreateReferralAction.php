<?php

namespace App\Actions\Referrals;

use App\Data\Referrals\ReferralData;
use App\Enums\AssociateRole;
use App\Enums\EmployeeRole;
use App\Enums\ReferralStatus;
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
        if ($user && $user->hasRole(AssociateRole::ASSOCIATE->value) && $associate->category && $offering->category) {
            if (strtolower($associate->category) === strtolower($offering->category->name)) {
                throw ValidationException::withMessages([
                    'offering_id' => ["Conflicto de intereses: No puedes referir servicios de tu misma categoría ({$associate->category})."],
                ]);
            }
        }

        $referral = DB::transaction(function () use ($data) {
            $metadata = $data->metadata ?? [];

            return Referral::create([
                'associate_id' => $data->associate_id,
                'offering_id' => $data->offering_id,
                'sector_id' => $data->sector_id,
                'status' => $data->status?->value ?? ReferralStatus::Prospect->value,
                'metadata' => $metadata,
                'notes' => $data->notes,
                'consent_confirmed' => $data->consent_confirmed,
            ]);
        });

        // Notify Admins and specific offering emails
        try {
            $recipients = User::role(EmployeeRole::values())->pluck('email')->toArray();

            Log::info('📧 Email Debug - Admin recipients from DB:', $recipients);

            if (! empty($offering->notification_emails)) {
                $recipients = array_unique(array_merge($recipients, $offering->notification_emails));
            }

            Log::info('📧 Email Debug - Final recipients (with offering emails):', $recipients);
            Log::info('📧 Email Debug - Current mail config:', [
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from' => config('mail.from'),
            ]);

            if (! empty($recipients)) {
                Mail::to($recipients)->send(new NewReferralAlertMail($referral, $user ?? auth()->user()));
                Log::info('📧 Email SENT successfully to: '.implode(', ', $recipients));
            } else {
                Log::warning('📧 Email NOT sent - no recipients found!');
            }
        } catch (\Exception $e) {
            Log::error('Failed to send new referral alert: '.$e->getMessage());
        }

        return $referral;
    }
}
