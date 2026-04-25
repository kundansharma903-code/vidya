<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('omr_upload_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_id')->constrained();
            $table->foreignId('uploaded_by')->constrained('users');

            $table->string('file_path', 500);
            $table->string('file_name');
            $table->integer('file_size'); // bytes

            $table->enum('status', ['uploaded', 'validating', 'matching', 'completed', 'failed'])->default('uploaded');
            $table->integer('total_rows')->nullable();
            $table->integer('matched_rows')->nullable();
            $table->integer('unmatched_rows')->nullable();

            $table->text('error_log')->nullable();
            $table->json('validation_errors')->nullable();

            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['institute_id', 'test_id'], 'idx_institute_test');
            $table->index('status', 'idx_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omr_upload_batches');
    }
};
