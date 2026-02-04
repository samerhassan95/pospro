<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Updating domain limits for all plans...\n\n";

// Update all plans
$updated = DB::table('plans')->update([
    'addon_domain_limit' => 5,
    'subdomain_limit' => 10,
]);

echo "Updated {$updated} plan(s).\n\n";

// Verify the update
$plans = DB::table('plans')->select('id', 'subscriptionName', 'addon_domain_limit', 'subdomain_limit')->get();

echo "Current plan limits:\n";
echo "-------------------\n";
foreach ($plans as $plan) {
    echo "ID: {$plan->id} | Name: {$plan->subscriptionName} | Addon Domains: {$plan->addon_domain_limit} | Subdomains: {$plan->subdomain_limit}\n";
}

echo "\nDone! Now clear the cache:\n";
echo "php artisan cache:clear\n";
