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
        Schema::create('backup_logs', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('path')->nullable();
            $table->enum('type', ['automatic', 'manual'])->default('automatic');
            $table->bigInteger('size')->default(0); // Size in bytes
            $table->enum('status', ['completed', 'failed'])->default('completed');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backup_logs');
    }
};



