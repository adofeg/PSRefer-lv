<?php

namespace App\Actions\Referrals;

use App\Data\Referrals\ReferralData;
use App\Enums\ReferralStatus;
use App\Enums\RoleName;
use App\Mail\NewReferralAlertMail;
use App\Models\Referral;
use App\Models\Offering;
use App\Models\User;
use App\Models\Associate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CreateReferralAction
{
  public function execute(ReferralData $data): Referral
  {
    $offering = Offering::with('category')->findOrFail($data->offering_id);
    $associate = Associate::findOrFail($data->associate_id);
    $user = $associate->user;

    // CONFLICT OF INTEREST VALIDATION (Parity with JS)
    // Associates cannot refer services in their own category
    if ($user->hasRole(RoleName::Associate->value) && $associate->category && $offering->category) {
      if (strtolower($associate->category) === strtolower($offering->category->name)) {
            throw ValidationException::withMessages([
          'offering_id' => ["Conflicto de intereses: No puedes referir servicios de tu misma categoría ({$associate->category})."]
            ]);
        }
    }

    $referral = DB::transaction(function () use ($data) {
      return Referral::create([
        'associate_id' => $data->associate_id,
        'offering_id' => $data->offering_id,
        'client_name' => $data->client_name,
        'client_contact' => $data->client_contact,
        'status' => $data->status?->value ?? ReferralStatus::Prospect->value,
        'metadata' => $data->metadata,
        'notes' => $data->notes,
      ]);
    });

    // Notify Admins
    try {
        $admins = User::role(RoleName::adminRoles())->get();
        if ($admins->isNotEmpty()) {
          Mail::to($admins)->send(new NewReferralAlertMail($referral, $user));
        }
    } catch (\Exception $e) {
        Log::error('Failed to send new referral alert: ' . $e->getMessage());
    }

    return $referral;
  }
}
