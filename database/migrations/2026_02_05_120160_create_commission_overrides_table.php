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
        Schema::create('commission_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('associate_id')->constrained('associates')->cascadeOnDelete();
            $table->foreignId('offering_id')->nullable()->constrained('offerings')->cascadeOnDelete();
            $table->decimal('base_commission', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['associate_id', 'offering_id', 'is_active'], 'co_lookup_idx');
            $table->unique(['associate_id', 'offering_id', 'deleted_at'], 'co_assoc_offer_deleted_uq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_overrides');
    }
};
