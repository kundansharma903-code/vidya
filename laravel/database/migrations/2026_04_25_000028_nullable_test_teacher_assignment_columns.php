<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('test_teacher_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable()->change();
            $table->integer('question_start')->nullable()->change();
            $table->integer('question_end')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('test_teacher_assignments', function (Blueprint $table) {
            $table->unsignedBigInteger('subject_id')->nullable(false)->change();
            $table->integer('question_start')->nullable(false)->change();
            $table->integer('question_end')->nullable(false)->change();
        });
    }
};
