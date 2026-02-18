<?php

/**
 * Check Domain Settings
 * 
 * This script checks the domain settings and provides recommendations
 * Run: php check_domain_settings.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Option;
use Modules\CustomDomainAddon\App\Models\Domain;

echo "=== Checking Domain Settings ===\n\n";

// Check if CustomDomainAddon is enabled
$statusFile = base_path('modules_statuses.json');
if (file_exists($statusFile)) {
    $moduleStatuses = json_decode(file_get_contents($statusFile), true);
    $isEnabled = $moduleStatuses['CustomDomainAddon'] ?? false;
    
    echo "CustomDomainAddon Status: " . ($isEnabled ? "✅ Enabled" : "❌ Disabled") . "\n\n";
    
    if (!$isEnabled) {
        echo "⚠️  Module is disabled. Enable it in modules_statuses.json\n";
        exit(0);
    }
} else {
    echo "❌ modules_statuses.json not found!\n";
    exit(1);
}

// Check domain settings
echo "Domain Settings:\n";
echo str_repeat("-", 50) . "\n";

$domainSettings = Option::where('key', 'domain-setting')->first();

if ($domainSettings) {
    $settings = $domainSettings->value;
    
    $sslRequired = $settings['ssl_required'] ?? 'off';
    $automaticApprove = $settings['automatic_approve'] ?? 'off';
    
    echo "SSL Required: " . ($sslRequired === 'on' ? "✅ ON" : "⚠️  OFF") . "\n";
    echo "Automatic Approve: " . ($automaticApprove === 'on' ? "✅ ON" : "⚠️  OFF") . "\n\n";
    
    if ($automaticApprove === 'on') {
        echo "⚠️  WARNING: Automatic approval is ON\n";
        echo "   This may cause issues on production servers due to:\n";
        echo "   - DNS resolution failures\n";
        echo "   - Firewall blocking outgoing requests\n";
        echo "   - HTTP timeout issues\n\n";
        echo "💡 RECOMMENDATION: Set automatic_approve to 'off' for production\n\n";
    }
} else {
    echo "⚠️  Domain settings not found in database\n";
    echo "   Creating default settings...\n";
    
    Option::create([
        'key' => 'domain-setting',
        'value' => [
            'ssl_required' => 'off',
            'automatic_approve' => 'off'
        ]
    ]);
    
    echo "✅ Default settings created\n\n";
}

// Check existing domains
echo "\nExisting Domains:\n";
echo str_repeat("-", 50) . "\n";

if (class_exists('Modules\CustomDomainAddon\App\Models\Domain')) {
    $domains = Domain::all();
    
    if ($domains->count() > 0) {
        echo "Total Domains: " . $domains->count() . "\n\n";
        
        foreach ($domains as $domain) {
            $statusText = match($domain->status) {
                0 => "⏳ Pending",
                1 => "✅ Approved",
                2 => "❌ Rejected",
                default => "❓ Unknown"
            };
            
            echo "Domain: {$domain->domain}\n";
            echo "  Status: {$statusText}\n";
            echo "  Verified: " . ($domain->is_verified ? "✅" : "❌") . "\n";
            echo "  SSL: " . ($domain->is_ssl_enabled ? "✅" : "❌") . "\n";
            echo "  Business ID: {$domain->business_id}\n";
            echo "\n";
        }
    } else {
        echo "No domains found\n";
    }
} else {
    echo "❌ Domain model not found. Module may not be properly installed.\n";
}

// Recommendations
echo "\n" . str_repeat("=", 50) . "\n";
echo "RECOMMENDATIONS FOR PRODUCTION\n";
echo str_repeat("=", 50) . "\n";
echo "1. Set automatic_approve to 'off'\n";
echo "2. Set ssl_required to 'off' (unless you have SSL for all domains)\n";
echo "3. Manually approve domains from Admin Panel\n";
echo "4. Check server firewall allows outgoing HTTP requests\n";
echo "5. Ensure DNS resolution works on the server\n";
echo "6. Monitor logs: storage/logs/laravel.log\n";

echo "\n";
