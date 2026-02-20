<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Business;
use App\Models\Plan;
use App\Models\PlanSubscribe;
use App\Models\BusinessCategory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateShopOwner extends Command
{
    protected $signature = 'shop:create {email} {password} {--name=Shop Owner}';
    protected $description = 'Create a new shop owner with full addon access';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $name = $this->option('name');

        $this->info('Creating shop owner with full addon access...');

        // Get or create business category
        $category = BusinessCategory::first();
        if (!$category) {
            $category = BusinessCategory::create([
                'name' => 'General',
                'status' => 1,
            ]);
            $this->info('✓ Business category created');
        }

        // Get or create plan
        $plan = Plan::first();
        if (!$plan) {
            $plan = Plan::create([
                'subscriptionName' => 'Premium',
                'duration' => 365,
                'subscriptionPrice' => 0,
                'status' => 1,
                'allow_multibranch' => 1,
                'addon_domain_limit' => 999,
                'subdomain_limit' => 999,
                'features' => json_encode([]),
            ]);
            $this->info('✓ Plan created');
        }

        // Create business
        $business = Business::create([
            'business_category_id' => $category->id,
            'companyName' => $name . ' Business',
            'will_expire' => '2035-12-31',
            'address' => 'Business Address',
            'email' => $email,
            'phoneNumber' => '1234567890',
            'subscriptionDate' => now(),
            'remainingShopBalance' => 0,
            'shopOpeningBalance' => 0,
            'status' => 1,
            'meta' => json_encode([
                'show_company_name' => 1,
                'show_phone_number' => 1,
                'show_address' => 1,
                'show_email' => 1,
                'show_vat_title' => 1,
                'show_vat_no' => 1,
            ]),
        ]);

        $this->info('✓ Business created: ' . $business->companyName);

        // Create plan subscription
        $planSubscribe = PlanSubscribe::create([
            'plan_id' => $plan->id,
            'business_id' => $business->id,
            'price' => 0,
            'payment_status' => 'paid',
            'duration' => 365,
            'allow_multibranch' => 1,
            'addon_domain_limit' => 999,
            'subdomain_limit' => 999,
        ]);

        // Update business with plan subscription
        $business->update([
            'plan_subscribe_id' => $planSubscribe->id,
        ]);

        $this->info('✓ Plan subscription created with unlimited addons');

        // Create shop owner user
        $user = User::create([
            'business_id' => $business->id,
            'name' => $name,
            'email' => $email,
            'role' => 'shop-owner',
            'phone' => '1234567890',
            'lang' => 'en',
            'password' => Hash::make($password),
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $this->info('✓ Shop owner created');

        $this->newLine();
        $this->info('🎉 Shop owner created successfully!');
        $this->newLine();
        $this->table(
            ['Field', 'Value'],
            [
                ['Email', $email],
                ['Password', $password],
                ['Name', $name],
                ['Role', 'Shop Owner'],
                ['Business', $business->companyName],
                ['Plan', $plan->subscriptionName],
                ['Addons', 'All enabled (unlimited)'],
                ['Expiry', '2035-12-31'],
            ]
        );

        return Command::SUCCESS;
    }
}
