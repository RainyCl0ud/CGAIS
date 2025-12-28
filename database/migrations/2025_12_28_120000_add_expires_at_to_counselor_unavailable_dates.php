<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('counselor_unavailable_dates', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('is_unavailable');
        });

        // Populate existing rows: set expires_at to the start of the next day
        $rows = DB::table('counselor_unavailable_dates')->get();
        foreach ($rows as $row) {
            if ($row->date) {
                $expires = Carbon::parse($row->date, 'Asia/Manila')->addDay()->startOfDay();
                DB::table('counselor_unavailable_dates')
                    ->where('id', $row->id)
                    ->update(['expires_at' => $expires->toDateTimeString()]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('counselor_unavailable_dates', function (Blueprint $table) {
            $table->dropColumn('expires_at');
        });
    }
};
