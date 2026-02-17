<?php

/**
 * Approve Domain
 * 
 * This script approves a domain so it can be accessed
 * Run: php approve_domain.php yourdomain.com
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Modules\CustomDomainAddon\App\Models\Domain;

if ($argc < 2) {
    echo "Usage: php approve_domain.php <domain>\n";
    echo "Example: php approve_domain.php nomuposs.com\n";
    exit(1);
}

$domainName = $argv[1];

echo "=== Approving Domain ===\n\n";
echo "Domain: {$domainName}\n\n";

$domain = Domain::where('domain', $domainName)->first();

if (!$domain) {
    echo "❌ Domain not found in database!\n";
    echo "\nSearching for similar domains...\n";
    
    $allDomains = Domain::all();
    if ($allDomains->count() > 0) {
        echo "\nAvailable domains:\n";
        foreach ($allDomains as $d) {
            echo "  - {$d->domain} (Status: {$d->status}, Verified: {$d->is_verified})\n";
        }
    } else {
        echo "No domains found in database.\n";
    }
    exit(1);
}

echo "Found domain:\n";
echo "  ID: {$domain->id}\n";
echo "  Domain: {$domain->domain}\n";
echo "  Business ID: {$domain->business_id}\n";
echo "  Status: {$domain->status}\n";
echo "  Verified: {$domain->is_verified}\n";
echo "  SSL: {$domain->is_ssl_enabled}\n\n";

// Approve the domain
$domain->update([
    'status' => 1,
    'is_verified' => 1,
]);

echo "✅ Domain approved successfully!\n\n";
echo "New status:\n";
echo "  Status: 1 (Approved)\n";
echo "  Verified: 1 (Yes)\n\n";

echo "✨ You can now access the domain!\n";
