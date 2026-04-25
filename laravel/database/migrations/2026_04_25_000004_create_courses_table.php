<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 20);
            $table->enum('exam_type', ['NEET', 'JEE_MAIN', 'JEE_ADVANCED', 'OTHER']);
            $table->year('target_year');
            $table->integer('duration_months');
            $table->integer('total_questions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('institute_id', 'idx_institute');
            $table->index('exam_type', 'idx_exam_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
