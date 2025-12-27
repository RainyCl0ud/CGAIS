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
            $table->string('availability_status')
                ->default('AVAILABLE')
                ->after('role');

            $table->time('unavailable_from')
                ->nullable()
                ->after('availability_status');

            $table->time('unavailable_to')
                ->nullable()
                ->after('unavailable_from');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['availability_status', 'unavailable_from', 'unavailable_to']);
        });
    }
};




