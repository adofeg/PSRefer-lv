<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();

            // Core
            $table->foreignId('associate_id')->nullable()->constrained('associates')->nullOnDelete();
            $table->foreignId('offering_id')->constrained('offerings');
            $table->string('client_name');
            $table->string('client_contact')->nullable();
            $table->string('status')->default('Prospecto'); // Prospecto, Contactado, Negociación, Cerrado

            // Financial Details (Merged from add_total_payment_to_referrals_table)
            $table->decimal('deal_value', 12, 2)->nullable();
            $table->decimal('revenue_generated', 12, 2)->nullable();
            $table->string('contract_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('down_payment', 10, 2)->default(0);
            $table->decimal('agency_fee', 10, 2)->default(0);

            // Meta
            $table->text('notes')->nullable();
            $table->jsonb('metadata')->nullable();

            $table->timestamp('closed_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('associate_id');
            $table->index('offering_id');
            $table->index('status');
            $table->index(['associate_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index('created_at');
            $table->index('closed_at');
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
