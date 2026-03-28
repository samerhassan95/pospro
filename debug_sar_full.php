<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$currencies = \App\Models\Currency::where('code', 'SAR')->get();
foreach ($currencies as $c) {
    echo "ID: " . $c->id . " | Code: " . $c->code . " | Symbol: " . $c->symbol . " | Content: [" . bin2hex($c->symbol) . "]" . PHP_EOL;
}
