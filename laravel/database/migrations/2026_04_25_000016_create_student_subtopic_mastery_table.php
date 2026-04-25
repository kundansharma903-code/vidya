<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_subtopic_mastery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('curriculum_node_id')->constrained('curriculum_nodes');
            $table->foreignId('subject_id')->constrained();

            // Cumulative counters (updated after every test)
            $table->integer('total_questions_attempted')->default(0);
            $table->integer('total_questions_correct')->default(0);
            $table->decimal('total_marks_earned', 8, 2)->default(0);
            $table->decimal('total_marks_possible', 8, 2)->default(0);

            // Computed percentages
            $table->decimal('mastery_percentage', 5, 2)->default(0);   // correct/attempted * 100
            $table->decimal('accuracy_percentage', 5, 2)->default(0);  // marks_earned/marks_possible * 100

            $table->decimal('last_test_score', 5, 2)->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'curriculum_node_id'], 'unq_student_subtopic');
            $table->index(['student_id', 'subject_id'], 'idx_student_subject');
            $table->index('mastery_percentage', 'idx_mastery');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subtopic_mastery');
    }
};
