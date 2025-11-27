<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->string('signature', 255)->nullable()->after('intervention_other');
        });
    }

    public function down()
    {
        Schema::table('personal_data_sheets', function (Blueprint $table) {
            $table->dropColumn('signature');
        });
    }
};