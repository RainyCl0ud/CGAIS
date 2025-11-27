<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->string('intervention', 10)->nullable()->after('health_condition_specify');
            $table->json('intervention_types')->nullable()->after('intervention');
            $table->string('tutorial_subjects')->nullable()->after('intervention_types');
            $table->text('intervention_other')->nullable()->after('tutorial_subjects');
        });
    }

    public function down()
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn(['intervention', 'intervention_types', 'tutorial_subjects', 'intervention_other']);
        });
    }
};