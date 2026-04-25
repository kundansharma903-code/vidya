<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('logo_path', 500)->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('timezone', 50)->default('Asia/Kolkata');
            $table->boolean('is_active')->default(true);
            $table->enum('subscription_tier', ['trial', 'basic', 'pro', 'enterprise'])->default('trial');
            $table->integer('student_limit')->default(500);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutes');
    }
};
