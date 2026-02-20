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
        Schema::table('payment_types', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_types', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('business_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('payment_types', 'balance')) {
                $table->decimal('balance', 15, 3)->default(0)->after('name');
            }
            if (!Schema::hasColumn('payment_types', 'opening_balance')) {
                $table->decimal('opening_balance', 15, 3)->default(0)->after('name');
            }
            if (!Schema::hasColumn('payment_types', 'opening_date')) {
                $table->date('opening_date')->nullable()->after('name');
            }
            if (!Schema::hasColumn('payment_types', 'show_in_invoice')) {
                $table->boolean('show_in_invoice')->default(1)->after('name');
            }
            if (!Schema::hasColumn('payment_types', 'meta')) {
                $table->longText('meta')->nullable()->after('name');
            }
            if (!Schema::hasColumn('payment_types', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('stocks', 'variant_name')) {
                $table->string('variant_name')->nullable()->after('productDealerPrice');
            }
            if (!Schema::hasColumn('stocks', 'variation_data')) {
                $table->longText('variation_data')->nullable()->after('productDealerPrice');
            }
            if (!Schema::hasColumn('stocks', 'serial_numbers')) {
                $table->longText('serial_numbers')->nullable()->after('productDealerPrice');
            }
            if (!Schema::hasColumn('stocks', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'variation_ids')) {
                $table->text('variation_ids')->nullable()->after('productCode');
            }
            if (!Schema::hasColumn('products', 'has_serial')) {
                $table->integer('has_serial')->default(0)->after('productCode');
            }
            if (!Schema::hasColumn('products', 'warranty_guarantee_info')) {
                $table->longText('warranty_guarantee_info')->nullable()->after('productCode');
            }
        });

        Schema::table('sale_details', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_details', 'warranty_guarantee_info')) {
                $table->longText('warranty_guarantee_info')->nullable()->after('quantities');
            }
            if (!Schema::hasColumn('sale_details', 'discount')) {
                $table->double('discount')->default(0)->after('price');
            }
        });

        Schema::table('gateways', function (Blueprint $table) {
            if (!Schema::hasColumn('gateways', 'platform')) {
                $table->longText('platform')->nullable()->after('name');
            }
        });

        Schema::table('businesses', function (Blueprint $table) {
            if (!Schema::hasColumn('businesses', 'meta')) {
                $table->longText('meta')->nullable()->after('vat_name');
            }
        });

        Schema::table('parties', function (Blueprint $table) {
            if (!Schema::hasColumn('parties', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('business_id')->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('payment_types', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'opening_balance', 'opening_date', 'show_in_invoice', 'meta', 'balance','deleted_at']);
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['variant_name', 'variation_data', 'serial_numbers']);
            $table->dropSoftDeletes();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['variation_ids', 'warranty_guarantee_info','has_serial']);
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropColumn(['warranty_guarantee_info', 'discount']);
        });

        Schema::table('gateways', function (Blueprint $table) {
            $table->dropColumn('platform');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('meta');
        });

        Schema::table('parties', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });
    }
};
