<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$currencies = DB::table('currencies')->where('code', 'SAR')->get();
foreach ($currencies as $c) {
    echo "ID: " . $c->id . "\n";
    echo "Name: " . $c->name . "\n";
    echo "Code: " . $c->code . "\n";
    echo "Symbol: " . $c->symbol . "\n";
    echo "--------------------\n";
}
