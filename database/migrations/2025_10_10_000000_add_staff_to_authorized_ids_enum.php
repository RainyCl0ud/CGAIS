<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddStaffToAuthorizedIdsEnum extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the 'type' enum to add 'staff' option.
        DB::statement("ALTER TABLE authorized_ids MODIFY COLUMN type ENUM('student', 'faculty', 'staff') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the 'type' enum back to original without 'staff'
        DB::statement("ALTER TABLE authorized_ids MODIFY COLUMN type ENUM('student', 'faculty') NOT NULL");
    }
}
