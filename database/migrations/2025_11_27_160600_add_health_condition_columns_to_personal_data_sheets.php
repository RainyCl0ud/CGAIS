<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->string('health_condition', 10)->nullable()->after('concerns');
            $table->text('health_condition_specify')->nullable()->after('health_condition');
        });
    }

    public function down()
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn(['health_condition', 'health_condition_specify']);
        });
    }
};