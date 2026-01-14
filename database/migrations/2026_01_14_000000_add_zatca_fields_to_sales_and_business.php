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
        Schema::table('sales', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->text('invoice_hash')->nullable();
            $table->text('previous_hash')->nullable();
            $table->text('cryptographic_stamp')->nullable(); // For Phase 2
            $table->string('zatca_status')->default('PENDING')->comment('PENDING, REPORTED, CLEARED, FAILED');
            $table->json('zatca_response')->nullable();
            $table->string('xml_path')->nullable();
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->text('zatca_setting')->nullable()->comment('JSON: csr_config, csid, private_key, secrets');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'uuid',
                'invoice_hash',
                'previous_hash',
                'cryptographic_stamp',
                'zatca_status',
                'zatca_response',
                'xml_path'
            ]);
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('zatca_setting');
        });
    }
};
