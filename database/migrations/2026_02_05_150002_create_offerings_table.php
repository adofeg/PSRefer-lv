<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offerings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('owner_id')->nullable()->constrained('users');
            $table->string('type'); // 'product', 'service'
            $table->string('category')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2)->nullable();
            $table->decimal('base_commission', 10, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->nullable();

            $table->jsonb('form_schema')->nullable();
            $table->jsonb('commission_config')->nullable();
            $table->jsonb('commission_rules')->nullable();

            $table->boolean('is_active')->default(true);
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offerings');
    }
};
