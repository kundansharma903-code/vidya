<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('code', 20)->nullable();
            $table->string('timing_label', 50)->nullable();
            $table->integer('capacity')->default(50);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['institute_id', 'course_id'], 'idx_institute_course');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
