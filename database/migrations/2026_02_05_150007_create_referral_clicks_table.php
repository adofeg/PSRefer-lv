<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_clicks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('referrer_id')->nullable()->constrained('users');
            $table->foreignUuid('offering_id')->nullable()->constrained('offerings');
            $table->string('link_type')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->timestamp('clicked_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_clicks');
    }
};
