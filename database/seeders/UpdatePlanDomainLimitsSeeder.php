<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class UpdatePlanDomainLimitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all plans to have domain limits
        // You can customize these values based on your plan tiers
        
        Plan::query()->update([
            'addon_domain_limit' => 5,    // Allow 5 custom domains
            'subdomain_limit' => 10,      // Allow 10 subdomains
        ]);

        // Or set different limits for different plans
        // Example: Free plan gets 0, paid plans get more
        
        // $freePlan = Plan::where('subscriptionPrice', 0)->first();
        // if ($freePlan) {
        //     $freePlan->update([
        //         'addon_domain_limit' => 0,
        //         'subdomain_limit' => 1,
        //     ]);
        // }
        
        // $paidPlans = Plan::where('subscriptionPrice', '>', 0)->get();
        // foreach ($paidPlans as $plan) {
        //     $plan->update([
        //         'addon_domain_limit' => 5,
        //         'subdomain_limit' => 10,
        //     ]);
        // }

        $this->command->info('Plan domain limits updated successfully!');
    }
}
