<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn([
                'deal_value',
                'revenue_generated',
                'payment_method',
                'down_payment',
                'agency_fee',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('referrals', function (Blueprint $table) {
            $table->decimal('deal_value', 15, 2)->nullable();
            $table->decimal('revenue_generated', 15, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('down_payment', 15, 2)->nullable();
            $table->decimal('agency_fee', 15, 2)->nullable();
        });
    }
};
