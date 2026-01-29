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
        // Add B2B fields to parties table
        Schema::table('parties', function (Blueprint $table) {
            $table->string('zatca_type')->default('b2c')->after('type')->comment('b2c or b2b');
            $table->string('vat_number', 15)->nullable()->after('zatca_type')->comment('Required for B2B');
            $table->string('building_number')->nullable()->after('address');
            $table->string('street_name')->nullable()->after('building_number');
            $table->string('district')->nullable()->after('street_name');
            $table->string('city')->nullable()->after('district');
            $table->string('postal_code', 10)->nullable()->after('city');
            $table->string('country_code', 2)->default('SA')->after('postal_code');
        });

        // Add B2B fields to businesses table
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('building_number')->nullable()->after('address');
            $table->string('street_name')->nullable()->after('building_number');
            $table->string('district')->nullable()->after('street_name');
            $table->string('city')->nullable()->after('district');
            $table->string('postal_code', 10)->nullable()->after('city');
            $table->string('country_code', 2)->default('SA')->after('postal_code');
        });

        // Add invoice_type to sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->string('invoice_type')->default('b2c')->after('type')->comment('b2c or b2b');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->dropColumn([
                'zatca_type',
                'vat_number',
                'building_number',
                'street_name',
                'district',
                'city',
                'postal_code',
                'country_code'
            ]);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'building_number',
                'street_name',
                'district',
                'city',
                'postal_code',
                'country_code'
            ]);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('invoice_type');
        });
    }
};
