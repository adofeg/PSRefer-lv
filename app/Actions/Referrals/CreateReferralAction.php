<?php

namespace App\Actions\Referrals;

use App\Data\Referrals\ReferralData;
use App\Models\Referral;
use Illuminate\Support\Facades\DB;

class CreateReferralAction
{
  public function execute(ReferralData $data): Referral
  {
    return DB::transaction(function () use ($data) {
      return Referral::create([
        'user_id' => $data->user_id,
        'offering_id' => $data->offering_id,
        'client_name' => $data->client_name,
        'client_contact' => $data->client_contact,
        'status' => $data->status,
        'metadata' => $data->metadata,
        'notes' => $data->notes,
      ]);
    });
  }
}
