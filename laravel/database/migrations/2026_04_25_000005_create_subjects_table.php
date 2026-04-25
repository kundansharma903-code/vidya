<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->char('code', 1);
            $table->enum('exam_type', ['NEET', 'JEE', 'BOTH'])->default('BOTH');
            $table->integer('display_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['institute_id', 'code'], 'unq_code_institute');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
