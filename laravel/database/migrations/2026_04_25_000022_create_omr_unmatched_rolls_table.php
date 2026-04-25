<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('omr_unmatched_rolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('omr_upload_batch_id')->constrained('omr_upload_batches')->cascadeOnDelete();

            $table->string('roll_in_excel', 30);
            // Best fuzzy match suggestion (Levenshtein ≤ 2)
            $table->foreignId('suggested_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->integer('suggestion_distance')->nullable();

            $table->enum('action', ['pending', 'matched', 'skipped', 'added_new'])->default('pending');
            $table->foreignId('matched_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['omr_upload_batch_id', 'action'], 'idx_batch_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('omr_unmatched_rolls');
    }
};
