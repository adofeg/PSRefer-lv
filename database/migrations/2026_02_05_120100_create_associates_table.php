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
        Schema::create('associates', function (Blueprint $table) {
            $table->id();
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('category')->nullable();
            $table->jsonb('payment_info')->nullable();
            $table->foreignId('referrer_id')->nullable()->constrained('associates');
            $table->timestamps();
            $table->softDeletes();

            $table->index('referrer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('associates');
    }
};
