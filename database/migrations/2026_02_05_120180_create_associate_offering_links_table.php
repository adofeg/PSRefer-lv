<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('associate_offering_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('associate_id')->constrained('associates')->cascadeOnDelete();
            $table->foreignId('offering_id')->constrained('offerings')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['associate_id', 'offering_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('associate_offering_links');
    }
};
