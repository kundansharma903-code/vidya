<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();

            $table->string('test_code', 50)->unique();
            $table->string('name');
            $table->enum('test_type', ['dpt', 'weekly', 'mock', 'flt', 'chapter', 'revision']);
            $table->date('test_date');
            $table->integer('duration_minutes');
            $table->integer('total_questions');
            $table->integer('total_marks');

            // Marking scheme
            $table->decimal('correct_marks', 4, 2)->default(4.00);
            $table->decimal('incorrect_marks', 4, 2)->default(-1.00);
            $table->decimal('unattempted_marks', 4, 2)->default(0.00);

            $table->enum('status', ['draft', 'blueprint_ready', 'conducted', 'responses_uploaded', 'analyzed', 'archived'])->default('draft');
            $table->foreignId('created_by')->constrained('users');

            $table->timestamp('conducted_at')->nullable();
            $table->timestamp('responses_uploaded_at')->nullable();
            $table->timestamp('analyzed_at')->nullable();
            $table->timestamps();

            $table->index(['institute_id', 'status'], 'idx_institute_status');
            $table->index('test_date', 'idx_test_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
