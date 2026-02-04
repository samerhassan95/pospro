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
        // Add fields to businesses table
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('commercial_registration', 50)->nullable()->after('vat_no');
            $table->string('additional_id', 50)->nullable()->after('commercial_registration');
            $table->string('bank_account_number', 50)->nullable()->after('additional_id');
            $table->string('bank_name', 100)->nullable()->after('bank_account_number');
        });

        // Add fields to parties table
        Schema::table('parties', function (Blueprint $table) {
            $table->string('commercial_registration', 50)->nullable()->after('vat_number');
            $table->string('additional_id', 50)->nullable()->after('commercial_registration');
        });

        // Add fields to sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->date('supply_date')->nullable()->after('saleDate');
            $table->string('po_number', 50)->nullable()->after('supply_date');
            $table->string('contract_number', 50)->nullable()->after('po_number');
            $table->string('payment_terms', 100)->nullable()->after('contract_number');
            $table->string('payment_means', 50)->nullable()->after('payment_terms');
            $table->string('shipping_address_line1', 255)->nullable()->after('payment_means');
            $table->string('shipping_address_line2', 255)->nullable()->after('shipping_address_line1');
            $table->string('shipping_city', 100)->nullable()->after('shipping_address_line2');
            $table->string('shipping_postal_code', 20)->nullable()->after('shipping_city');
        });

        // Add fields to sale_details table
        Schema::table('sale_details', function (Blueprint $table) {
            $table->string('item_code', 50)->nullable()->after('product_id');
            $table->string('unit_of_measure', 20)->nullable()->after('item_code');
            $table->decimal('list_price', 10, 2)->nullable()->after('price');
            $table->decimal('discount_percent', 5, 2)->nullable()->after('list_price');
            $table->decimal('net_price', 10, 2)->nullable()->after('discount_percent');
            $table->decimal('tax_per_item', 10, 2)->nullable()->after('net_price');
            $table->string('tax_exemption_reason', 255)->nullable()->after('tax_per_item');
        });

        // Add fields to plan_subscribes table
        Schema::table('plan_subscribes', function (Blueprint $table) {
            $table->string('service_code', 50)->nullable()->after('plan_id');
            $table->date('service_start_date')->nullable()->after('service_code');
            $table->date('service_end_date')->nullable()->after('service_start_date');
            $table->date('tax_period_start')->nullable()->after('service_end_date');
            $table->date('tax_period_end')->nullable()->after('tax_period_start');
            $table->string('po_number', 50)->nullable()->after('tax_period_end');
            $table->string('contract_number', 50)->nullable()->after('po_number');
            $table->string('payment_terms', 100)->nullable()->after('contract_number');
            $table->string('payment_means', 50)->nullable()->after('payment_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'commercial_registration',
                'additional_id',
                'bank_account_number',
                'bank_name'
            ]);
        });

        Schema::table('parties', function (Blueprint $table) {
            $table->dropColumn([
                'commercial_registration',
                'additional_id'
            ]);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'supply_date',
                'po_number',
                'contract_number',
                'payment_terms',
                'payment_means',
                'shipping_address_line1',
                'shipping_address_line2',
                'shipping_city',
                'shipping_postal_code'
            ]);
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropColumn([
                'item_code',
                'unit_of_measure',
                'list_price',
                'discount_percent',
                'net_price',
                'tax_per_item',
                'tax_exemption_reason'
            ]);
        });

        Schema::table('plan_subscribes', function (Blueprint $table) {
            $table->dropColumn([
                'service_code',
                'service_start_date',
                'service_end_date',
                'tax_period_start',
                'tax_period_end',
                'po_number',
                'contract_number',
                'payment_terms',
                'payment_means'
            ]);
        });
    }
};
