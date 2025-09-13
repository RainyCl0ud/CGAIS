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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action', 255)->index();
            $table->json('data')->nullable();
            $table->string('ip_address', 45)->index(); // IPv6 compatible
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->string('method', 10)->nullable();
            $table->timestamp('timestamp')->index();
            $table->string('session_id', 255)->nullable()->index();
            $table->string('request_id', 255)->nullable()->index();
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low')->index();
            $table->string('threat_type', 100)->nullable()->index();
            $table->text('mitigation_taken')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Composite indexes for common queries
            $table->index(['user_id', 'timestamp']);
            $table->index(['action', 'timestamp']);
            $table->index(['ip_address', 'timestamp']);
            $table->index(['risk_level', 'timestamp']);
            $table->index(['user_id', 'action', 'timestamp']);

            // Full-text search on action only (data is JSON and cannot be indexed)
            $table->fullText(['action'], 'audit_logs_search');
        });

        // Add partitioning for better performance (optional)
        // This would require additional setup in production
        // DB::statement('ALTER TABLE audit_logs PARTITION BY RANGE (YEAR(timestamp))');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
