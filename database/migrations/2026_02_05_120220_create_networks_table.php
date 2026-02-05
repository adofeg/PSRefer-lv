<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('networks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_associate_id')->constrained('associates')->onDelete('cascade');
            $table->foreignId('child_associate_id')->constrained('associates')->onDelete('cascade');
            $table->integer('level')->default(1);
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['parent_associate_id', 'child_associate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('networks');
    }
};
