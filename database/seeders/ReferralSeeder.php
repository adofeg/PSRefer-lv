<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferralSeeder extends Seeder
{
    public function run(): void
    {
        // Data from inmersivaapp.sql
        $referrals = [
            [
                'id' => 1,
                'associate_id' => 1,
                'offering_id' => 2, // Seguros de Vida
                'client_name' => 'Priscilla Marin', // OLD COLUMN
                'client_contact' => 'primarinb@gmail.com', // OLD COLUMN (Email)
                'status' => 'Contactado',
                'deal_value' => null,
                'revenue_generated' => null,
                'contract_id' => null,
                'payment_method' => null,
                'down_payment' => 0.00,
                'agency_fee' => 0.00,
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
                'client_name' => 'peggy ojeda', // OLD COLUMN
                'client_contact' => '7867027699', // OLD COLUMN (Phone)
                'status' => 'Cerrado',
                'deal_value' => null,
                'revenue_generated' => 9.00,
                'contract_id' => null,
                'payment_method' => null,
                'down_payment' => 0.00,
                'agency_fee' => 0.00,
                'notes' => null, 
                'metadata' => '[]',
                'closed_at' => null,
                'paid_at' => null,
                'created_at' => '2026-02-11 15:24:30',
                'updated_at' => '2026-02-11 15:33:18',
                'deleted_at' => null,
            ],
        ];

        foreach ($referrals as $data) {
            // Transform to new Schema
            $metadata = json_decode($data['metadata'], true) ?? [];
            
            // Migrate Identity Fields
            $metadata['client_name'] = $data['client_name'];
            
            // Detect Email vs Phone
            $contact = $data['client_contact'];
            if (filter_var($contact, FILTER_VALIDATE_EMAIL)) {
                $metadata['client_email'] = $contact;
            } else {
                $metadata['client_phone'] = $contact;
            }

            // Prepare Insert Data (columns must match current schema)
            $insertData = [
                'id' => $data['id'],
                'associate_id' => $data['associate_id'],
                'offering_id' => $data['offering_id'],
                'status' => $data['status'],
                'deal_value' => $data['deal_value'],
                'revenue_generated' => $data['revenue_generated'],
                'contract_id' => $data['contract_id'],
                'payment_method' => $data['payment_method'],
                'down_payment' => $data['down_payment'],
                'agency_fee' => $data['agency_fee'],
                'notes' => $data['notes'],
                'consent_confirmed' => true, // Default to true for historical data
                'metadata' => json_encode($metadata), // Store transformed metadata
                'closed_at' => $data['closed_at'],
                'paid_at' => $data['paid_at'],
                'created_at' => $data['created_at'],
                'updated_at' => $data['updated_at'],
                'deleted_at' => $data['deleted_at'],
            ];

            // Use DB::table to bypass Model events/timestamps override
            DB::table('referrals')->insert($insertData);
        }
    }
}
