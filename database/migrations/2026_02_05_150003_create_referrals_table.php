<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users');
            $table->foreignUuid('offering_id')->nullable()->constrained('offerings');

            $table->string('client_name');
            $table->string('client_contact')->nullable();
            $table->string('status')->default('Prospecto');

            $table->decimal('deal_value', 10, 2)->nullable();
            $table->decimal('revenue_generated', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->jsonb('metadata')->nullable();

            // Financial
            $table->string('contract_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('down_payment', 10, 2)->nullable();
            $table->decimal('agency_fee', 10, 2)->nullable();

            $table->timestamps();
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
