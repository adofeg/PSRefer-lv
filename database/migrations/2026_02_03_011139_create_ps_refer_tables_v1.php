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
        // Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password'); // Standard Laravel column
            $table->string('phone')->nullable();
            $table->string('role')->default('associate');
            $table->text('logo_url')->nullable();
            $table->decimal('balance', 10, 2)->default(0);
            $table->string('category')->nullable();

            // Financial & Compliance
            $table->jsonb('payment_info')->nullable();
            $table->string('w9_status')->default('pending');
            $table->text('w9_file_url')->nullable();

            // Self-referencing FK
            // Self-referencing FK (Moved to after create)
            // $table->foreignUuid('referred_by_id')->nullable()->references('id')->on('users');

            // Security
            $table->timestamp('password_changed_at')->nullable();
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->string('reset_token')->nullable();
            $table->timestamp('reset_token_expires')->nullable();

            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('referred_by_id')->nullable()->references('id')->on('users');
        });

        // System Settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamp('updated_at')->useCurrent();
        });

        // Offerings (Products/Services)
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

        // Referrals (Leads)
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

        // Commissions
        Schema::create('commissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('referral_id')->nullable()->constrained('referrals');
            $table->foreignUuid('user_id')->constrained('users'); // Beneficiary

            $table->decimal('amount', 10, 2);
            $table->decimal('commission_percentage', 5, 2)->nullable();
            $table->string('commission_type')->default('direct');
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();

            // Recurrence
            $table->string('recurrence_type')->default('one_time');
            $table->string('recurrence_interval')->nullable();
            $table->timestamp('recurrence_end_date')->nullable();
            // Start Self-ref
            // $table->foreignUuid('parent_commission_id')->nullable()->references('id')->on('commissions');

            $table->timestamps();
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->foreignUuid('parent_commission_id')->nullable()->references('id')->on('commissions');
        });

        // User Offering Commissions (Overrides)
        Schema::create('user_offering_commissions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('offering_id')->constrained('offerings')->cascadeOnDelete();
            $table->decimal('commission_rate', 5, 2);
            $table->timestamps();

            $table->unique(['user_id', 'offering_id']);
        });

        // User Offering Links
        Schema::create('user_offering_links', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('offering_id')->constrained('offerings')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->primary(['user_id', 'offering_id']);
        });

        // Referral Clicks
        Schema::create('referral_clicks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('referrer_id')->nullable()->constrained('users');
            $table->foreignUuid('offering_id')->nullable()->constrained('offerings');
            $table->string('link_type')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();

            $table->timestamp('clicked_at')->useCurrent();
        });

        // Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('entity');
            $table->uuid('entity_id')->nullable();
            $table->string('action');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->jsonb('previous_data')->nullable();
            $table->jsonb('new_data')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity', 'entity_id']);
        });

        Schema::create('security_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('event_type');
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('referral_clicks');
        Schema::dropIfExists('user_offering_links');
        Schema::dropIfExists('user_offering_commissions');
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('offerings');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('users');
    }
};
