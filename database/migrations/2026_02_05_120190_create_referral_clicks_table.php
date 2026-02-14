<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referral_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_associate_id')->nullable()->constrained('associates')->nullOnDelete();
            $table->foreignId('offering_id')->nullable()->constrained('offerings')->nullOnDelete();
            $table->string('link_type')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->timestamp('clicked_at')->useCurrent();

            $table->index('referrer_associate_id');
            $table->index('offering_id');
            $table->index('link_type');
            $table->index('clicked_at');
            $table->index(['referrer_associate_id', 'clicked_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_clicks');
    }
};
