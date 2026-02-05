<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RestaurantTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tables = [
            // Row 1
            ['table_name' => 'Table 1', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '20px', 'position_left' => '20px', 'is_custom' => false, 'status' => 'free'],
            ['table_name' => 'Table 2', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '20px', 'position_left' => '150px', 'is_custom' => false, 'status' => 'free'],
            ['table_name' => 'Table 3', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '20px', 'position_left' => '280px', 'is_custom' => false, 'status' => 'free'],
            ['table_name' => 'Table 4', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '20px', 'position_left' => '410px', 'is_custom' => false, 'status' => 'free'],
            
            // Row 2
            ['table_name' => 'Table 5', 'table_type' => 'rounded', 'chair_count' => 6, 'position_top' => '150px', 'position_left' => '20px', 'is_custom' => false, 'status' => 'free'],
            ['table_name' => 'Table 6', 'table_type' => 'rounded', 'chair_count' => 6, 'position_top' => '150px', 'position_left' => '180px', 'is_custom' => false, 'status' => 'free'],
            ['table_name' => 'Table 7', 'table_type' => 'rounded', 'chair_count' => 6, 'position_top' => '150px', 'position_left' => '340px', 'is_custom' => false, 'status' => 'free'],
            
            // Row 3
            ['table_name' => 'Table 8', 'table_type' => 'rectangle', 'chair_count' => 4, 'position_top' => '300px', 'position_left' => '20px', 'is_custom' => false, 'status' => 'free'],
            ['table_name' => 'Table 9', 'table_type' => 'rectangle', 'chair_count' => 4, 'position_top' => '300px', 'position_left' => '200px', 'is_custom' => false, 'status' => 'free'],
            ['table_name' => 'Table 10', 'table_type' => 'rectangle', 'chair_count' => 4, 'position_top' => '300px', 'position_left' => '380px', 'is_custom' => false, 'status' => 'free'],
        ];

        // Get current business ID (assuming first business or you can modify this)
        $businessId = DB::table('businesses')->first()->id ?? 1;

        foreach ($tables as $table) {
            DB::table('restaurant_tables')->insert(array_merge($table, [
                'business_id' => $businessId,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
