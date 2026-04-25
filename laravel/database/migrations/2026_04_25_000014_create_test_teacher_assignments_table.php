<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('subject_id')->constrained();
            $table->integer('question_start');
            $table->integer('question_end');
            $table->timestamps();

            $table->unique(['test_id', 'subject_id'], 'unq_test_subject');
            $table->index('teacher_id', 'idx_teacher');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_teacher_assignments');
    }
};
