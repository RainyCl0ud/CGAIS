<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Convert enum to varchar so we can store dynamic service slugs
        DB::statement("ALTER TABLE `appointments` MODIFY `counseling_category` VARCHAR(191) NULL;");
    }

    public function down()
    {
        // Revert back to the original enum definition
        DB::statement("ALTER TABLE `appointments` MODIFY `counseling_category` ENUM('conduct_intake_interview','information_services','internal_referral_services','counseling_services','conduct_exit_interview','consultation') NULL;");
    }
};
