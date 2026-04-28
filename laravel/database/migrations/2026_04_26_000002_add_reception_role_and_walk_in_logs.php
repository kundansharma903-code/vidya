<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Add 'reception' to the role enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner','academic_head','admin','sub_admin','teacher','typist','reception') NOT NULL");

        // Create walk_in_logs table
        Schema::create('walk_in_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reception_user_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('test_id')->nullable();
            $table->timestamp('viewed_at')->useCurrent();
            $table->enum('query_type', ['result_lookup', 'general_inquiry'])->default('result_lookup');

            $table->foreign('reception_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('test_id')->references('id')->on('tests')->onDelete('set null');

            $table->index(['reception_user_id', 'viewed_at']);
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('walk_in_logs');
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner','academic_head','admin','sub_admin','teacher','typist') NOT NULL");
    }
};
