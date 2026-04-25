<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();

            // Identifiers
            $table->string('roll_number', 30);
            $table->string('enrollment_number', 30)->nullable();

            // Personal
            $table->string('name');
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['M', 'F', 'O'])->nullable();

            // Contact
            $table->string('phone', 20)->nullable();
            $table->string('parent_phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            // Academic
            $table->date('admission_date')->nullable();
            $table->enum('medium', ['english', 'hindi'])->default('english');
            $table->decimal('previous_score', 5, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['institute_id', 'roll_number'], 'unq_roll_institute');
            $table->index('batch_id', 'idx_batch');
            $table->index(['institute_id', 'is_active'], 'idx_institute_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
