<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // NULL = system action

            $table->string('action', 100); // 'test.created', 'student.updated', etc.
            $table->string('entity_type', 50)->nullable(); // 'Test', 'Student', etc.
            $table->unsignedBigInteger('entity_id')->nullable();

            $table->text('description')->nullable();
            $table->json('changes')->nullable(); // Before/after for updates
            $table->json('metadata')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();

            // No updated_at — logs are immutable
            $table->timestamp('created_at')->nullable();

            $table->index(['institute_id', 'created_at'], 'idx_institute_created');
            $table->index('user_id', 'idx_user');
            $table->index('action', 'idx_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
