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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('counselor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('appointment_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'])->default('pending');
            $table->enum('type', ['regular', 'urgent', 'follow_up'])->default('regular');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->text('counselor_notes')->nullable();
            $table->timestamps();
            
            $table->index(['appointment_date', 'start_time']);
            $table->index(['counselor_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
}; 