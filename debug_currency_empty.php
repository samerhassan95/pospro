<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check UserCurrency records
$userCurrencies = \DB::table('user_currencies')->get();
foreach ($userCurrencies as $uc) {
    echo "BusID: " . $uc->business_id . " | Sym: [" . $uc->symbol . "] | Code: " . $uc->code . "\n";
}

// Check if any business has SAR but no symbol
$bad = \DB::table('user_currencies')->where('code', 'SAR')->where(function($q){
    $q->whereNull('symbol')->orWhere('symbol', '');
})->get();
if ($bad->count() > 0) {
    echo "FOUD " . $bad->count() . " SAR currencies with EMPTY symbol!\n";
    foreach($bad as $b) {
        echo "BusID: " . $b->business_id . "\n";
    }
} else {
    echo "No bad SAR currencies found.\n";
}
