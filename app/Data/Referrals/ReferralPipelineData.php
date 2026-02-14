<?php

namespace App\Data\Referrals;

use App\Models\Referral;
use Spatie\LaravelData\Data;

class ReferralPipelineData extends Data
{
    public function __construct(
        public int $id,
        public string $status,
        public string $client_name,
        public ?string $client_contact,
        public ?float $revenue_generated,
        public ?string $created_at,
        public ?array $offering,
        public ?array $user,
    ) {}

    public static function fromModel(Referral $referral): self
    {
        $associateUser = $referral->associate?->user;

        return new self(
            id: (int) $referral->id,
            status: (string) $referral->status,
            client_name: (string) $referral->client_name,
            client_contact: $referral->client_contact,
            revenue_generated: $referral->revenue_generated !== null ? (float) $referral->revenue_generated : null,
            created_at: $referral->created_at?->toISOString(),
            offering: $referral->offering ? [
                'id' => (int) $referral->offering->id,
                'name' => $referral->offering->name,
            ] : null,
            user: $associateUser ? [
                'id' => (int) $associateUser->id,
                'name' => $associateUser->name,
            ] : null,
        );
    }
}
