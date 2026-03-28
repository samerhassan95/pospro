<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Use DB to avoid any potential model issues
$userCurrencies = \DB::table('user_currencies')->get();
echo "User Currencies count: " . count($userCurrencies) . "\n";
foreach ($userCurrencies as $uc) {
    echo "ID: " . $uc->id . " | BusID: " . $uc->business_id . " | Sym: [" . $uc->symbol . "] | Code: " . $uc->code . " | Pos: " . $uc->position . "\n";
}

$default = \DB::table('currencies')->where('is_default', 1)->first();
if ($default) {
    echo "Default Currency: " . $default->code . " Symbol: [" . $default->symbol . "] (" . bin2hex($default->symbol) . ")\n";
} else {
    echo "No default currency found in DB.\n";
}
