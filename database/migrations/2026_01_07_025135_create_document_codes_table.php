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
        Schema::create('document_codes', function (Blueprint $table) {
            $table->id();
            $table->string('document_code_no')->default('FM-USTP-GCS-02');
            $table->string('revision_no')->default('00');
            $table->string('effective_date')->default('03.17.25');
            $table->string('page_no')->default('1 of 2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_codes');
    }
};
