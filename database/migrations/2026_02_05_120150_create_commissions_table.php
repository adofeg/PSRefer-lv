<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_id')->nullable()->constrained('referrals');
            $table->foreignId('associate_id')->constrained('associates'); // Beneficiary

            $table->decimal('amount', 10, 2);
            $table->decimal('commission_percentage', 5, 2)->nullable();
            $table->string('commission_type')->default('direct');
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();

            // Recurrence
            $table->string('recurrence_type')->default('one_time');
            $table->string('recurrence_interval')->nullable();
            $table->timestamp('recurrence_end_date')->nullable();

            $table->timestamps();

            $table->index('associate_id');
            $table->index('referral_id');
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->foreignId('parent_commission_id')->nullable()->references('id')->on('commissions');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
