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
            $table->foreignId('associate_id')->constrained('associates')->onDelete('cascade');
            $table->foreignId('offering_id')->constrained('offerings')->onDelete('cascade');
            $table->decimal('commission_rate', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['associate_id', 'offering_id']);
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
