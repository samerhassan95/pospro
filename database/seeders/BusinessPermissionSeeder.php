<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class BusinessPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // ZATCA Settings permissions
            'zatca-settings-read',
            'zatca-settings-create', 
            'zatca-settings-update',
            'zatca-settings-delete',
            
            // Moyasar Settings permissions
            'moyasar-settings-read',
            'moyasar-settings-create',
            'moyasar-settings-update', 
            'moyasar-settings-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}