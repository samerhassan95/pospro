<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\RestaurantTable;
use App\Models\Business;

class RestaurantTableSeeder extends Seeder {
    public function run() {
        $businesses = Business::all();
        
        foreach ($businesses as $business) {
            $defaultTables = [
                ['table_name' => 'Ta1', 'table_type' => 'rectangle-h10', 'chair_count' => 10, 'position_top' => '40px', 'position_left' => '380px'],
                ['table_name' => 'Tb1', 'table_type' => 'rounded', 'chair_count' => 6, 'position_top' => '50px', 'position_right' => '60px'],
                ['table_name' => 'Ta3', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '200px', 'position_right' => '80px'],
                ['table_name' => 'Ta8', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '280px', 'position_left' => '120px'],
                ['table_name' => 'Tb2', 'table_type' => 'rounded', 'chair_count' => 6, 'position_bottom' => '60px', 'position_left' => '80px'],
                ['table_name' => 'Ta4', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '380px', 'position_left' => '420px'],
                ['table_name' => 'Ta5', 'table_type' => 'circle', 'chair_count' => 4, 'position_bottom' => '60px', 'position_left' => '380px'],
                ['table_name' => 'Ta6', 'table_type' => 'rectangle-h', 'chair_count' => 8, 'position_bottom' => '60px', 'position_right' => '120px'],
                ['table_name' => 'Ta9', 'table_type' => 'circle', 'chair_count' => 2, 'position_top' => '180px', 'position_left' => '280px'],
                ['table_name' => 'Ta10', 'table_type' => 'rectangle', 'chair_count' => 12, 'position_top' => '220px', 'position_right' => '260px'],
            ];
            
            foreach ($defaultTables as $tableData) {
                $tableData['business_id'] = $business->id;
                $tableData['status'] = 'free';
                $tableData['is_custom'] = false;
                $tableData['is_active'] = true;
                
                RestaurantTable::create($tableData);
            }
        }
    }
}