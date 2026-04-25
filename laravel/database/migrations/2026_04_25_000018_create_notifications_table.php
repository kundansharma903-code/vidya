<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Recipient

            $table->string('type', 60); // 'at_risk_alert', 'test_results_ready', etc.
            $table->string('title');
            $table->text('message');
            // Extra context: entity_id, entity_type, action_url
            $table->json('data')->nullable();

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_read'], 'idx_user_unread');
            $table->index(['institute_id', 'created_at'], 'idx_institute_created');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
