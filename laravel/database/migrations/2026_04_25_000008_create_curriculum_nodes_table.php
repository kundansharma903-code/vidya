<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('curriculum_nodes')->cascadeOnDelete();

            $table->enum('level', ['chapter', 'topic', 'subtopic']);
            $table->string('name');
            $table->string('code', 20);
            $table->string('full_code', 30); // Pre-computed: "P-MEC-KIN-01"

            $table->integer('display_order')->default(0);
            $table->decimal('weightage', 5, 2)->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['institute_id', 'full_code'], 'unq_code_institute');
            $table->index(['subject_id', 'level'], 'idx_subject_level');
            $table->index('parent_id', 'idx_parent');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_nodes');
    }
};
