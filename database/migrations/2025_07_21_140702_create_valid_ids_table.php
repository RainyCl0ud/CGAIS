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
        Schema::create('valid_ids', function (Blueprint $table) {
            $table->id();
            $table->string('id_code')->unique();
            $table->enum('type', ['student', 'faculty']);
            $table->boolean('is_used')->default(false);
            $table->string('email')->nullable(); // Optional, for future cross-checking
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valid_ids');
    }
};
