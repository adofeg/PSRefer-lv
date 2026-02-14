<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offerings', function (Blueprint $table) {
            $table->id();

            // Core
            $table->foreignId('owner_employee_id')->nullable()->constrained('employees');
            $table->string('type'); // 'product', 'service'
            $table->string('category')->nullable();

            // New Category ID (from redundant migration)
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->string('name');
            $table->text('description')->nullable();

            // Pricing
            $table->string('commission_type')->default('percentage'); // 'percentage', 'fixed'
            $table->decimal('base_commission', 10, 2)->default(0);

            // Configs
            $table->jsonb('form_schema')->nullable();
            $table->jsonb('commission_config')->nullable();
            $table->jsonb('commission_rules')->nullable();

            $table->boolean('is_active')->default(true);
            $table->jsonb('metadata')->nullable();
            $table->jsonb('notification_emails')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('category_id');
            $table->index('owner_employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offerings');
    }
};
