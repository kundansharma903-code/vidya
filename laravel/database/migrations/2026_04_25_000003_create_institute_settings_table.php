<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institute_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institute_id')->unique()->constrained()->cascadeOnDelete();

            // AI settings
            $table->string('gemini_api_key')->nullable();
            $table->enum('ai_mode', ['auto', 'gemini_only', 'template_only'])->default('auto');
            $table->integer('ai_circuit_breaker_threshold')->default(5);
            $table->integer('ai_cache_ttl')->default(3600);

            // Email / SMTP
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->default(587);
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->enum('smtp_encryption', ['tls', 'ssl', 'none'])->default('tls');
            $table->boolean('notification_email_enabled')->default(false);

            // Notification rules
            $table->decimal('at_risk_threshold', 5, 2)->default(40.00);
            $table->boolean('weekly_digest_enabled')->default(true);

            // Academic
            $table->string('academic_year_format', 20)->default('YYYY-YYYY');

            // Catch-all for future settings
            $table->json('custom_settings')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institute_settings');
    }
};
