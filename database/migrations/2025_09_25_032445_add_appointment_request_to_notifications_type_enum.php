<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add missing notification types to the existing enum values
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('appointment_reminder', 'appointment_confirmed', 'appointment_cancelled', 'appointment_request', 'appointment_approved', 'appointment_rejected', 'appointment_rescheduled', 'appointment', 'urgent', 'system', 'general') DEFAULT 'general'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the added notification types from the enum values
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('appointment_reminder', 'appointment_confirmed', 'appointment_cancelled', 'general') DEFAULT 'general'");
    }
};
