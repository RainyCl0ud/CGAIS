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
        // Get existing column names
        $columns = DB::getSchemaBuilder()->getColumnListing('personal_data_sheets');
        
        Schema::table('personal_data_sheets', function (Blueprint $table) use ($columns) {
            // Only add columns that don't exist
            if (!in_array('family_description_other', $columns)) {
                $table->string('family_description_other')->nullable()->after('family_description');
            }
            if (!in_array('living_situation_other', $columns)) {
                $table->string('living_situation_other')->nullable()->after('living_situation');
            }
            if (!in_array('health_condition', $columns)) {
                $table->string('health_condition')->nullable()->after('concerns');
            }
            if (!in_array('health_condition_specify', $columns)) {
                $table->text('health_condition_specify')->nullable()->after('health_condition');
            }
            if (!in_array('intervention', $columns)) {
                $table->string('intervention')->nullable()->after('health_condition_specify');
            }
            if (!in_array('intervention_types', $columns)) {
                $table->json('intervention_types')->nullable()->after('intervention');
            }
            if (!in_array('tutorial_subjects', $columns)) {
                $table->string('tutorial_subjects')->nullable()->after('intervention_types');
            }
            if (!in_array('intervention_other', $columns)) {
                $table->text('intervention_other')->nullable()->after('tutorial_subjects');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn([
                'family_description_other',
                'living_situation_other',
                'health_condition',
                'health_condition_specify',
                'intervention',
                'intervention_types',
                'tutorial_subjects',
                'intervention_other',
            ]);
        });
    }
};
