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
        Schema::create('authorized_ids', function (Blueprint $table) {
            $table->id();
            $table->string('id_number')->unique();
            $table->enum('type', ['student', 'faculty']);
            $table->boolean('is_used')->default(false);
            $table->foreignId('registered_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('used_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('used_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'is_used']);
            $table->index('id_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorized_ids');
    }
};
