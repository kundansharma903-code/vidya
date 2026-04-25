<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_batch_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'batch_id'], 'unq_user_batch');
            $table->index('batch_id', 'idx_batch');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_batch_assignments');
    }
};
