<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('monthly_salary', 10, 2)->nullable()->after('is_active');
            $table->date('tenure_start')->nullable()->after('monthly_salary');
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->decimal('monthly_fee', 8, 2)->nullable()->after('capacity');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['monthly_salary', 'tenure_start']);
        });
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('monthly_fee');
        });
    }
};
