<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add moyasar_setting to businesses table
        if (!Schema::hasColumn('businesses', 'moyasar_setting')) {
            Schema::table('businesses', function (Blueprint $table) {
                $table->text('moyasar_setting')->nullable()->comment('JSON: api_key, publishable_key');
            });
        }

        // Add Moyasar to gateways table for Admin
        $exists = DB::table('gateways')->where('namespace', 'App\Library\Moyasar')->exists();
        if (!$exists) {
            DB::table('gateways')->insert([
                'name' => 'Moyasar',
                'mode' => 'Sandbox',
                'status' => 1,
                'namespace' => 'App\Library\Moyasar',
                'data' => json_encode([
                    'api_key' => '',
                    'publishable_key' => '',
                ]),
                'platform' => 'Both',
                'currency_id' => 1, // Default currency
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('moyasar_setting');
        });

        DB::table('gateways')->where('namespace', 'App\Library\Moyasar')->delete();
    }
};
