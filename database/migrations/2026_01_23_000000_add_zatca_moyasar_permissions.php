<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'zatca-settings-read',
            'zatca-settings-create', 
            'zatca-settings-update',
            'zatca-settings-delete',
            'moyasar-settings-read',
            'moyasar-settings-create',
            'moyasar-settings-update', 
            'moyasar-settings-delete',
        ];

        Permission::whereIn('name', $permissions)->delete();
    }
};