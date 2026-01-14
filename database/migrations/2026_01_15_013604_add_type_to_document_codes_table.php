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
        Schema::table('document_codes', function (Blueprint $table) {
            $table->enum('type', ['pds', 'feedback_form'])->default('pds');
        });

        // Set existing record type to 'pds'
        DB::table('document_codes')->update(['type' => 'pds']);

        // Insert new record for feedback_form
        DB::table('document_codes')->insert([
            'document_code_no' => 'FM-USTP-GCS-01',
            'revision_no' => '00',
            'effective_date' => '07.01.23',
            'type' => 'feedback_form',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete the feedback_form record
        DB::table('document_codes')->where('type', 'feedback_form')->delete();

        // Remove the type column
        Schema::table('document_codes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
