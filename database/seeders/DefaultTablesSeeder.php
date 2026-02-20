<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RestaurantTable;
use App\Models\Business;

class DefaultTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all businesses
        $businesses = Business::all();

        foreach ($businesses as $business) {
            // Check if tables already exist for this business
            $existingTables = RestaurantTable::where('business_id', $business->id)->count();
            
            if ($existingTables > 0) {
                $this->command->info("Tables already exist for business {$business->id}, skipping...");
                continue;
            }

            // Create default tables
            $defaultTables = [
                // Circle tables
                ['table_name' => 'Ta1', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '50px', 'position_left' => '50px'],
                ['table_name' => 'Ta2', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '50px', 'position_left' => '200px'],
                ['table_name' => 'Ta3', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '50px', 'position_left' => '350px'],
                ['table_name' => 'Ta4', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '50px', 'position_left' => '500px'],
                ['table_name' => 'Ta5', 'table_type' => 'circle', 'chair_count' => 4, 'position_top' => '50px', 'position_left' => '650px'],
                
                // Rounded tables
                ['table_name' => 'Ta6', 'table_type' => 'rounded', 'chair_count' => 6, 'position_top' => '200px', 'position_left' => '50px'],
                ['table_name' => 'Ta7', 'table_type' => 'rounded', 'chair_count' => 6, 'position_top' => '200px', 'position_left' => '250px'],
                ['table_name' => 'Ta8', 'table_type' => 'rounded', 'chair_count' => 6, 'position_top' => '200px', 'position_left' => '450px'],
                
                // Rectangle tables
                ['table_name' => 'Ta9', 'table_type' => 'rectangle', 'chair_count' => 4, 'position_top' => '350px', 'position_left' => '50px'],
                ['table_name' => 'Ta10', 'table_type' => 'rectangle', 'chair_count' => 4, 'position_top' => '350px', 'position_left' => '250px'],
                ['table_name' => 'Ta11', 'table_type' => 'rectangle', 'chair_count' => 4, 'position_top' => '350px', 'position_left' => '450px'],
                
                // Rectangle-h tables
                ['table_name' => 'Ta12', 'table_type' => 'rectangle-h', 'chair_count' => 6, 'position_top' => '500px', 'position_left' => '50px'],
                ['table_name' => 'Ta13', 'table_type' => 'rectangle-h', 'chair_count' => 6, 'position_top' => '500px', 'position_left' => '300px'],
                
                // Rectangle-h10 tables
                ['table_name' => 'Ta14', 'table_type' => 'rectangle-h10', 'chair_count' => 10, 'position_top' => '650px', 'position_left' => '50px'],
                ['table_name' => 'Ta15', 'table_type' => 'rectangle-h10', 'chair_count' => 10, 'position_top' => '650px', 'position_left' => '400px'],
            ];

            foreach ($defaultTables as $table) {
                RestaurantTable::create([
                    'business_id' => $business->id,
                    'table_name' => $table['table_name'],
                    'table_type' => $table['table_type'],
                    'chair_count' => $table['chair_count'],
                    'position_top' => $table['position_top'],
                    'position_left' => $table['position_left'],
                    'status' => 'free',
                    'is_custom' => false,
                    'is_active' => true,
                ]);
            }

            $this->command->info("Created default tables for business {$business->id}");
        }

        $this->command->info('Default tables seeding completed!');
    }
}
