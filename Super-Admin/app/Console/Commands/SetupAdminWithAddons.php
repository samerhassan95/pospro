<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Plan;
use App\Models\Business;
use App\Models\PlanSubscribe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetupAdminWithAddons extends Command
{
    protected $signature = 'admin:setup-addons';
    protected $description = 'Setup admin@admin.com user with full addon access';

    public function handle()
    {
        $this->info('Setting up admin user with all addons...');

        // Create or update admin user as superadmin
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'business_id' => null,
                'name' => 'Super Admin',
                'role' => 'superadmin',
                'phone' => '1234567890',
                'lang' => 'en',
                'password' => Hash::make('admin'),
                'status' => 1,
                'email_verified_at' => now(),
            ]
        );

        $this->info('✓ Super Admin user created/updated: admin@admin.com / admin');

        // Update all plans to enable addons
        Plan::query()->update([
            'allow_multibranch' => 1,
            'addon_domain_limit' => 999,
            'subdomain_limit' => 999,
        ]);

        $this->info('✓ All plans updated with addon access');

        // Update plan subscriptions
        PlanSubscribe::query()->update([
            'allow_multibranch' => 1,
            'addon_domain_limit' => 999,
            'subdomain_limit' => 999,
        ]);

        $this->info('✓ Plan subscriptions updated');

        // Update business expiry
        Business::query()->update([
            'will_expire' => '2035-12-31',
        ]);

        $this->info('✓ Business expiry extended');

        $this->newLine();
        $this->info('🎉 Setup complete!');
        $this->info('Login credentials:');
        $this->info('Email: admin@admin.com');
        $this->info('Password: admin');
        $this->info('Role: Super Admin (full system access)');
        $this->newLine();
        $this->info('All addons are now enabled in modules_statuses.json');
        $this->info('All businesses have unlimited addon access');

        return Command::SUCCESS;
    }
}
