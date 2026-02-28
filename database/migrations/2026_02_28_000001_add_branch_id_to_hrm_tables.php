<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add branch_id to holidays table if it doesn't exist
        if (Schema::hasTable('holidays') && !Schema::hasColumn('holidays', 'branch_id')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('business_id')->constrained('branches')->nullOnDelete();
            });
        }

        // Add branch_id to attendances table if it doesn't exist
        if (Schema::hasTable('attendances') && !Schema::hasColumn('attendances', 'branch_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('business_id')->constrained('branches')->nullOnDelete();
            });
        }

        // Add branch_id to leaves table if it doesn't exist
        if (Schema::hasTable('leaves') && !Schema::hasColumn('leaves', 'branch_id')) {
            Schema::table('leaves', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('business_id')->constrained('branches')->nullOnDelete();
            });
        }

        // Add branch_id to payrolls table if it doesn't exist
        if (Schema::hasTable('payrolls') && !Schema::hasColumn('payrolls', 'branch_id')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('business_id')->constrained('branches')->nullOnDelete();
            });
        }

        // Add branch_id to employees table if it doesn't exist
        if (Schema::hasTable('employees') && !Schema::hasColumn('employees', 'branch_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('business_id')->constrained('branches')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('holidays') && Schema::hasColumn('holidays', 'branch_id')) {
            Schema::table('holidays', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('attendances') && Schema::hasColumn('attendances', 'branch_id')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('leaves') && Schema::hasColumn('leaves', 'branch_id')) {
            Schema::table('leaves', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('payrolls') && Schema::hasColumn('payrolls', 'branch_id')) {
            Schema::table('payrolls', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }

        if (Schema::hasTable('employees') && Schema::hasColumn('employees', 'branch_id')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
