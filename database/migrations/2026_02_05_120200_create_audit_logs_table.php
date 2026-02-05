<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Standard Entity
            $table->string('entity')->nullable(); // Legacy
            $table->unsignedBigInteger('entity_id')->nullable(); // Legacy
            
            // Polymorphic Subject
            $table->nullableMorphs('auditable');
            
            // Event
            $table->string('action'); // CREATE, UPDATE, etc.
            $table->string('event_type')->nullable(); // more descriptive slug

            // Actor
            $table->nullableMorphs('actorable');

            // Changes
            $table->jsonb('previous_data')->nullable(); // old_value
            $table->jsonb('new_data')->nullable(); // new_value
            $table->text('description')->nullable(); 
            $table->jsonb('metadata')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['entity', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
