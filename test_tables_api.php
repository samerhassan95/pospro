<?php
/**
 * Test Tables API Endpoint
 * Run this file directly in browser: http://127.0.0.1:8000/test_tables_api.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate a request to the tables endpoint
$request = Illuminate\Http\Request::create('/business/tables', 'GET');
$request->headers->set('Accept', 'application/json');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

try {
    $response = $kernel->handle($request);
    
    echo "<h2>Tables API Test</h2>";
    echo "<h3>Status Code: " . $response->getStatusCode() . "</h3>";
    echo "<h3>Response:</h3>";
    echo "<pre>";
    echo $response->getContent();
    echo "</pre>";
    
    $kernel->terminate($request, $response);
} catch (Exception $e) {
    echo "<h2>Error:</h2>";
    echo "<pre>";
    echo $e->getMessage();
    echo "\n\n";
    echo $e->getTraceAsString();
    echo "</pre>";
}
