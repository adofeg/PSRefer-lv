<?php

namespace Database\Seeders;

use App\Models\Referral;
use App\Services\CommissionService;
use Illuminate\Database\Seeder;

class ReferralSeeder extends Seeder
{
    public function run(): void
    {
        $commissionService = app(CommissionService::class);

        // Data from inmersivaapp.sql
        $referrals = [
            [
                'id' => 1,
                'associate_id' => 1,
                'offering_id' => 2, // Seguros de Vida
                'client_name' => 'Priscilla Marin',
                'client_contact' => 'primarinb@gmail.com',
                'status' => 'Contactado',
                'contract_id' => null,
                'notes' => 'Test',
                'metadata' => '[]',
                'closed_at' => null,
                'paid_at' => null,
                'created_at' => '2026-02-10 04:20:55',
                'updated_at' => '2026-02-10 04:21:46',
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'associate_id' => 1,
                'offering_id' => 1, // Seguros de Salud
                'client_name' => 'peggy ojeda',
                'client_contact' => '7867027699',
                'status' => 'Cerrado',
                'contract_id' => 'CTR-202602-001',
                'notes' => 'Migrated from historical data',
                'metadata' => '[]',
                'closed_at' => '2026-02-11 15:33:18',
                'paid_at' => null,
                'created_at' => '2026-02-11 15:24:30',
                'updated_at' => '2026-02-11 15:33:18',
                'deleted_at' => null,
            ],
        ];

        foreach ($referrals as $data) {
            // Transform to new Schema
            $metadata = json_decode($data['metadata'], true) ?? [];
            $metadata['client_name'] = $data['client_name'];

            $contact = $data['client_contact'];
            if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                $metadata['client_email'] = $contact;
            } else {
                $metadata['client_phone'] = $contact;
            }

            $referral = Referral::create([
                'id' => $data['id'],
                'associate_id' => $data['associate_id'],
                'offering_id' => $data['offering_id'],
                'status' => $data['status'],
                'contract_id' => $data['contract_id'],
                'notes' => $data['notes'],
                'consent_confirmed' => true,
                'metadata' => $metadata,
                'closed_at' => $data['closed_at'],
                'paid_at' => $data['paid_at'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at'],
                'deleted_at' => $data['deleted_at'],
            ]);

            // Automatically trigger commissions for closed referrals
            if ($referral->status === 'Cerrado') {
                $commissionService->createAllCommissions($referral, $referral->offering);
            }
        }
    }
}
