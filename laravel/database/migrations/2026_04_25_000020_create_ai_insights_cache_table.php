<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_insights_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();

            // Unique key = hash(report_type + institute_id + entity_id + input_data_hash)
            $table->string('cache_key', 64)->unique();
            $table->string('report_type', 60); // 'batch_comparison', 'student_deep_dive', etc.
            $table->string('entity_type', 30)->nullable(); // 'batch', 'student', 'institute'
            $table->unsignedBigInteger('entity_id')->nullable();

            $table->longText('ai_response');
            $table->integer('tokens_used')->nullable();

            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['institute_id', 'report_type'], 'idx_institute_report');
            $table->index('expires_at', 'idx_expiry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_insights_cache');
    }
};
