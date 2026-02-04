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
        Schema::table('plan_subscribes', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->string('invoice_number')->nullable()->after('uuid');
            $table->string('invoice_type')->default('b2b')->after('invoice_number'); // Subscriptions usually B2B
            $table->string('zatca_status')->default('PENDING')->after('payment_status');
            $table->text('invoice_hash')->nullable();
            $table->text('previous_hash')->nullable();
            $table->longText('cryptographic_stamp')->nullable();
            $table->longText('zatca_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_subscribes', function (Blueprint $table) {
            $table->dropColumn([
                'uuid',
                'invoice_number',
                'invoice_type',
                'zatca_status',
                'invoice_hash',
                'previous_hash',
                'cryptographic_stamp',
                'zatca_response'
            ]);
        });
    }
};
