<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('referrals', 'client_name') && ! Schema::hasColumn('referrals', 'client_contact')) {
            return;
        }

        DB::table('referrals')->orderBy('id')->chunkById(100, function ($referrals): void {
            foreach ($referrals as $referral) {
                $metadata = [];
                if (! empty($referral->metadata)) {
                    $decoded = json_decode((string) $referral->metadata, true);
                    if (is_array($decoded)) {
                        $metadata = $decoded;
                    }
                }

                if (Schema::hasColumn('referrals', 'client_name') && ! empty($referral->client_name)) {
                    $metadata['client_name'] = $referral->client_name;
                }

                if (Schema::hasColumn('referrals', 'client_contact') && ! empty($referral->client_contact)) {
                    $metadata['client_contact'] = $referral->client_contact;
                }

                DB::table('referrals')->where('id', $referral->id)->update([
                    'metadata' => json_encode($metadata),
                ]);
            }
        });

        Schema::table('referrals', function (Blueprint $table): void {
            $columns = [];
            if (Schema::hasColumn('referrals', 'client_name')) {
                $columns[] = 'client_name';
            }
            if (Schema::hasColumn('referrals', 'client_contact')) {
                $columns[] = 'client_contact';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }

    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table): void {
            if (! Schema::hasColumn('referrals', 'client_name')) {
                $table->string('client_name')->nullable()->after('offering_id');
            }
            if (! Schema::hasColumn('referrals', 'client_contact')) {
                $table->string('client_contact')->nullable()->after('client_name');
            }
        });

        DB::table('referrals')->orderBy('id')->chunkById(100, function ($referrals): void {
            foreach ($referrals as $referral) {
                $metadata = [];
                if (! empty($referral->metadata)) {
                    $decoded = json_decode((string) $referral->metadata, true);
                    if (is_array($decoded)) {
                        $metadata = $decoded;
                    }
                }

                DB::table('referrals')->where('id', $referral->id)->update([
                    'client_name' => $metadata['client_name'] ?? null,
                    'client_contact' => $metadata['client_contact'] ?? null,
                ]);
            }
        });
    }
};
