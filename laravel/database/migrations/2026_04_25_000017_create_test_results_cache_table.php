<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_results_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();

            $table->decimal('total_marks', 7, 2)->default(0);
            $table->integer('total_correct')->default(0);
            $table->integer('total_incorrect')->default(0);
            $table->integer('total_unattempted')->default(0);

            $table->integer('rank_in_batch')->nullable();
            $table->integer('rank_in_course')->nullable();
            $table->decimal('percentile', 5, 2)->nullable();

            // Per-subject breakdown: { "P": { "marks": 72, "correct": 18, "rank": 3 }, ... }
            $table->json('subject_scores')->nullable();

            $table->timestamps();

            $table->unique(['test_id', 'student_id'], 'unq_test_student');
            $table->index(['test_id', 'batch_id'], 'idx_test_batch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_results_cache');
    }
};
