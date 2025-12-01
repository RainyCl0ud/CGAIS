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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'faculty', 'counselor', 'assistant', 'staff'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update any 'staff' records to 'assistant' to avoid data truncation
        DB::statement("UPDATE users SET role = 'assistant' WHERE role = 'staff'");

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'faculty', 'counselor', 'assistant'])->change();
        });
    }
};
