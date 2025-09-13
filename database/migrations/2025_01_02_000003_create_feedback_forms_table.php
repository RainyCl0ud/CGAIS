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
        Schema::create('feedback_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('counselor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('appointment_id')->nullable()->constrained()->onDelete('set null');
            
            // Feedback Categories
            $table->integer('counselor_rating')->nullable(); // 1-5 stars
            $table->integer('service_rating')->nullable(); // 1-5 stars
            $table->integer('facility_rating')->nullable(); // 1-5 stars
            $table->integer('overall_satisfaction')->nullable(); // 1-5 stars
            
            // Feedback Questions
            $table->text('counselor_feedback')->nullable();
            $table->text('service_feedback')->nullable();
            $table->text('suggestions')->nullable();
            $table->text('concerns')->nullable();
            
            // Additional Information
            $table->boolean('would_recommend')->nullable();
            $table->text('recommendation_reason')->nullable();
            $table->text('additional_comments')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['counselor_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_forms');
    }
}; 