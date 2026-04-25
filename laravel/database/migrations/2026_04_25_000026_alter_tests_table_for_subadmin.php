<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the FK constraint on course_id so we can make it nullable
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
        });

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable()->change();
            $table->string('pattern', 50)->nullable()->after('test_type');
            $table->decimal('invalid_marks', 4, 2)->default(-1.00)->after('unattempted_marks');
        });

        // Re-add FK as nullable-safe
        Schema::table('tests', function (Blueprint $table) {
            $table->foreign('course_id')->references('id')->on('courses')->nullOnDelete();
        });

        // Add 'scheduled' status to enum
        DB::statement("ALTER TABLE tests MODIFY COLUMN status ENUM('draft','scheduled','blueprint_ready','conducted','responses_uploaded','analyzed','archived') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropForeign(['course_id']);
            $table->dropColumn(['pattern', 'invalid_marks']);
        });

        DB::statement("ALTER TABLE tests MODIFY COLUMN status ENUM('draft','blueprint_ready','conducted','responses_uploaded','analyzed','archived') NOT NULL DEFAULT 'draft'");

        Schema::table('tests', function (Blueprint $table) {
            $table->unsignedBigInteger('course_id')->nullable(false)->change();
            $table->foreign('course_id')->references('id')->on('courses')->cascadeOnDelete();
        });
    }
};
