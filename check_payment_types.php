<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔍 Checking Payment Types...\n\n";

$paymentTypes = DB::table('payment_types')->get();

if ($paymentTypes->isEmpty()) {
    echo "❌ No payment types found!\n";
    echo "📝 Creating default payment types...\n\n";
    
    $types = [
        ['name' => 'Cash', 'status' => 1],
        ['name' => 'Card', 'status' => 1],
        ['name' => 'UPI', 'status' => 1],
        ['name' => 'Due', 'status' => 1],
    ];
    
    foreach ($types as $type) {
        DB::table('payment_types')->insert($type);
        echo "✅ Created: {$type['name']}\n";
    }
    
    echo "\n✅ Default payment types created!\n";
} else {
    echo "✅ Found " . $paymentTypes->count() . " payment types:\n\n";
    foreach ($paymentTypes as $type) {
        echo "   ID: {$type->id}\n";
        echo "   Name: {$type->name}\n";
        echo "   Status: {$type->status}\n\n";
    }
}
