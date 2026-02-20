<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddMissingPermissions extends Command
{
    protected $signature = 'permissions:add-missing';
    protected $description = 'Add all missing permissions for business features';

    public function handle()
    {
        $this->info('Adding missing permissions...');

        $permissions = [
            // Party Reports
            'customer-ledger.read',
            'supplier-ledger.read',
            'party-loss-profit.read',
            
            // Commissions
            'commissions.read',
            'commissions.create',
            'commissions.update',
            'commissions.delete',
            'sale-commissions.read',
            
            // Advanced Reports
            'product-loss-profit-reports.read',
            'top-product-reports.read',
            'combo-product-reports.read',
            'discount-product-reports.read',
            'product-purchase-reports.read',
            'product-sale-reports.read',
            
            // Top Reports (already in sidebar)
            'top-customers-reports.read',
            'top-suppliers-reports.read',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
            $this->info("✓ Created/Verified: {$permission}");
        }

        // Give all permissions to superadmin role
        $superadminRole = Role::where('name', 'superadmin')->first();
        if ($superadminRole) {
            $allPermissions = Permission::all();
            $superadminRole->syncPermissions($allPermissions);
            $this->info('✓ All permissions synced to superadmin role');
        }

        $this->info('✅ All missing permissions added successfully!');
        
        return 0;
    }
}
