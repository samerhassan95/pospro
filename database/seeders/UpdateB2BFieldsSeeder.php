<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateB2BFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing parties to have default zatca_type as b2c
        DB::table('parties')->whereNull('zatca_type')->update([
            'zatca_type' => 'b2c',
            'country_code' => 'SA'
        ]);

        // Update existing businesses to have default country_code
        DB::table('businesses')->whereNull('country_code')->update([
            'country_code' => 'SA'
        ]);

        // Update existing sales to have default invoice_type as b2c
        DB::table('sales')->whereNull('invoice_type')->update([
            'invoice_type' => 'b2c'
        ]);
    }
}
