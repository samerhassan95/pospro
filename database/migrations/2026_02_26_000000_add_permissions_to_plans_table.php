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
        Schema::table('plans', function (Blueprint $table) {
            // Permissions
            $table->boolean('allow_purchases')->default(1)->after('allow_multibranch');
            $table->boolean('allow_products')->default(1)->after('allow_purchases');
            $table->boolean('allow_warehouses')->default(1)->after('allow_products');
            $table->integer('warehouse_limit')->nullable()->after('allow_warehouses'); // null = unlimited
            $table->integer('branch_limit')->nullable()->after('warehouse_limit'); // null = unlimited
            $table->boolean('allow_stock')->default(1)->after('branch_limit');
            $table->boolean('allow_customers')->default(1)->after('allow_stock');
            $table->boolean('allow_suppliers')->default(1)->after('allow_customers');
            $table->boolean('allow_vat_settings')->default(1)->after('allow_suppliers');
            $table->boolean('allow_due_list')->default(1)->after('allow_vat_settings');
            $table->boolean('allow_finance')->default(1)->after('allow_due_list');
            $table->boolean('allow_commission')->default(1)->after('allow_finance');
            $table->boolean('allow_hrm')->default(1)->after('allow_commission');
            $table->boolean('allow_reports')->default(1)->after('allow_hrm');
            $table->boolean('allow_pos_app')->default(1)->after('allow_reports');
            $table->boolean('allow_store')->default(1)->after('allow_pos_app');
            $table->boolean('allow_sales')->default(1)->after('allow_store');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn([
                'allow_purchases',
                'allow_products',
                'allow_warehouses',
                'warehouse_limit',
                'branch_limit',
                'allow_stock',
                'allow_customers',
                'allow_suppliers',
                'allow_vat_settings',
                'allow_due_list',
                'allow_finance',
                'allow_commission',
                'allow_hrm',
                'allow_reports',
                'allow_pos_app',
                'allow_store',
                'allow_sales',
            ]);
        });
    }
};
