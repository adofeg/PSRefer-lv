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
            $table->foreignId('referral_id')->nullable()->constrained('referrals')->nullOnDelete();
            $table->foreignId('associate_id')->constrained('associates'); // Beneficiary
            $table->foreignId('parent_commission_id')->nullable()->constrained('commissions')->nullOnDelete();

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
            $table->softDeletes();

            $table->index('associate_id');
            $table->index('referral_id');
            $table->index('parent_commission_id');
            $table->index('status');
            $table->index(['associate_id', 'status']);
            $table->index('commission_type');
            $table->index('paid_at');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
