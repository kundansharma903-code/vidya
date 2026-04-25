<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->integer('question_number');
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('curriculum_node_id')->constrained('curriculum_nodes');
            $table->string('topic_code', 30); // Denormalized for query speed

            $table->char('correct_answer', 1);
            $table->enum('answer_type', ['mcq', 'integer', 'numerical'])->default('mcq');

            // Per-question overrides (null = use test-level defaults)
            $table->decimal('correct_marks', 4, 2)->nullable();
            $table->decimal('incorrect_marks', 4, 2)->nullable();

            $table->enum('difficulty', ['easy', 'medium', 'hard'])->nullable();
            $table->integer('expected_time_seconds')->nullable();
            $table->timestamps();

            $table->unique(['test_id', 'question_number'], 'unq_test_qnum');
            $table->index(['test_id', 'subject_id'], 'idx_test_subject');
            $table->index('curriculum_node_id', 'idx_curriculum');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_questions');
    }
};
