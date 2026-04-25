<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('institute_id')->constrained()->cascadeOnDelete();

            $table->boolean('can_upload_omr')->default(false);
            $table->boolean('can_view_all_students')->default(false);
            $table->boolean('can_edit_students')->default(false);
            $table->boolean('can_create_students')->default(false);
            $table->boolean('can_view_all_batches')->default(false);
            $table->boolean('can_view_audit_logs')->default(false);
            $table->boolean('can_manage_notifications')->default(false);

            // NULL = no restriction; JSON array of batch IDs = scoped access
            $table->json('scoped_batch_ids')->nullable();

            $table->string('template_used', 50)->nullable(); // 'response_manager', 'student_manager', 'custom'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_admin_permissions');
    }
};
