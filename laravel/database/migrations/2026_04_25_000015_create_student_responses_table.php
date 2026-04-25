<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_question_id')->constrained('test_questions');

            $table->char('submitted_answer', 1)->nullable(); // NULL = unattempted
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_awarded', 4, 2)->default(0);
            $table->integer('time_taken_seconds')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'test_question_id'], 'unq_student_question');
            $table->index(['test_id', 'student_id'], 'idx_test_student');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_responses');
    }
};
