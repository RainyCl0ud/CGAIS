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
        Schema::create('personal_data_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Personal Information
            $table->string('birth_date')->nullable();
            $table->string('birth_place')->nullable();
            $table->enum('sex', ['male', 'female'])->nullable();
            $table->enum('civil_status', ['single', 'married', 'widowed', 'separated', 'divorced'])->nullable();
            $table->string('citizenship')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('blood_type')->nullable();
            
            // Contact Information
            $table->string('mobile_number')->nullable();
            $table->string('telephone_number')->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('present_address')->nullable();
            
            // Family Information
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_contact')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_contact')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('guardian_contact')->nullable();
            
            // Educational Background
            $table->string('elementary_school')->nullable();
            $table->string('elementary_year_graduated')->nullable();
            $table->string('high_school')->nullable();
            $table->string('high_school_year_graduated')->nullable();
            $table->string('college')->nullable();
            $table->string('college_year_graduated')->nullable();
            $table->string('course')->nullable();
            $table->string('year_level')->nullable();
            $table->string('student_id_number')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_relationship')->nullable();
            $table->string('emergency_contact_number')->nullable();
            $table->text('emergency_contact_address')->nullable();
            
            // Health Information
            $table->text('medical_conditions')->nullable();
            $table->text('allergies')->nullable();
            $table->text('medications')->nullable();
            
            // Additional Information
            $table->text('hobbies')->nullable();
            $table->text('interests')->nullable();
            $table->text('goals')->nullable();
            $table->text('concerns')->nullable();
            
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_data_sheets');
    }
}; 