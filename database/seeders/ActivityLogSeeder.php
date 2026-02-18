<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Audit Logs
        $auditLogs = [
            [
                'id' => 1,
                'entity' => null, 'entity_id' => null,
                'auditable_type' => 'App\\Models\\Referral', 'auditable_id' => 1,
                'action' => 'UPDATE', 'event_type' => 'status_change',
                'actorable_type' => 'App\\Models\\User', 'actorable_id' => 1,
                'previous_data' => '{"status":"Prospecto"}',
                'new_data' => '{"status":"Contactado"}',
                'description' => 'Status changed from Prospecto to Contactado',
                'metadata' => '{"notes":"la llamamos y esta interesad EN COMPRAR EL 10 DE FEBRERO","note":"la llamamos y esta interesad EN COMPRAR EL 10 DE FEBRERO"}',
                'created_at' => '2026-02-10 04:21:46',
            ],
            [
                'id' => 2,
                'entity' => null, 'entity_id' => null,
                'auditable_type' => 'App\\Models\\Referral', 'auditable_id' => 2,
                'action' => 'UPDATE', 'event_type' => 'status_change',
                'actorable_type' => 'App\\Models\\User', 'actorable_id' => 1,
                'previous_data' => '{"status":"Prospecto"}',
                'new_data' => '{"status":"Contactado"}',
                'description' => 'Status changed from Prospecto to Contactado',
                'metadata' => '{"notes":"se llamo al cliente para pedirle la informacion","note":"se llamo al cliente para pedirle la informacion"}',
                'created_at' => '2026-02-11 15:26:08',
            ],
            [
                'id' => 3,
                'entity' => null, 'entity_id' => null,
                'auditable_type' => 'App\\Models\\Referral', 'auditable_id' => 2,
                'action' => 'UPDATE', 'event_type' => 'status_change',
                'actorable_type' => 'App\\Models\\User', 'actorable_id' => 1,
                'previous_data' => '{"status":"Contactado"}',
                'new_data' => '{"status":"Cerrado"}',
                'description' => 'Status changed from Contactado to Cerrado',
                'metadata' => '{"notes":"se le dio un seguro de salud Ambetter","note":"se le dio un seguro de salud Ambetter"}',
                'created_at' => '2026-02-11 15:33:18',
            ],
        ];

        DB::table('audit_logs')->insert($auditLogs);


        // 2. Security Logs (Login History) - Simplified list from dump
        $securityLogs = [
            ['id' => 1, 'event_type' => 'LOGIN_SUCCESS', 'actorable_type' => 'App\\Models\\Employee', 'actorable_id' => 2, 'email' => 'admin@psrefer.com', 'ip_address' => '38.25.2.4', 'user_agent' => 'Mozilla/5.0...', 'metadata' => '{"guard":"web"}', 'created_at' => '2026-02-10 04:06:37'],
            ['id' => 2, 'event_type' => 'LOGIN_SUCCESS', 'actorable_type' => 'App\\Models\\Associate', 'actorable_id' => 1, 'email' => 'partner@psrefer.com', 'ip_address' => '2803:5840:2006:3b00:7869:2c43:46af:decc', 'user_agent' => 'Mozilla/5.0...', 'metadata' => '{"guard":"web"}', 'created_at' => '2026-02-10 04:20:15'],
            ['id' => 3, 'event_type' => 'LOGIN_SUCCESS', 'actorable_type' => 'App\\Models\\Employee', 'actorable_id' => 1, 'email' => 'psadmin@psrefer.com', 'ip_address' => '2803:5840:2006:3b00:7869:2c43:46af:decc', 'user_agent' => 'Mozilla/5.0...', 'metadata' => '{"guard":"web"}', 'created_at' => '2026-02-10 04:21:19'],
            ['id' => 8, 'event_type' => 'LOGIN_SUCCESS', 'actorable_type' => 'App\\Models\\Employee', 'actorable_id' => 1, 'email' => 'psadmin@psrefer.com', 'ip_address' => '162.251.62.58', 'user_agent' => 'Mozilla/5.0 (iPhone)...', 'metadata' => '{"guard":"web"}', 'created_at' => '2026-02-18 14:34:13'],
            ['id' => 9, 'event_type' => 'LOGIN_SUCCESS', 'actorable_type' => 'App\\Models\\Employee', 'actorable_id' => 1, 'email' => 'psadmin@psrefer.com', 'ip_address' => '38.25.2.4', 'user_agent' => 'Mozilla/5.0...', 'metadata' => '{"guard":"web"}', 'created_at' => '2026-02-18 14:41:23'],
        ];
        
        DB::table('security_logs')->insert($securityLogs);
    }
}
