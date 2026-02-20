<?php
/**
 * Test script to verify tables backend integration
 * Run this from browser: http://your-domain.com/test_tables_backend.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

use App\Models\RestaurantTable;
use App\Models\TableReservation;
use App\Models\TableOrder;
use Illuminate\Support\Facades\DB;

echo "<h1>Tables Backend Integration Test</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .success { color: #10b981; font-weight: bold; }
    .error { color: #ef4444; font-weight: bold; }
    .info { color: #3b82f6; }
    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
    th { background: #f9fafb; font-weight: 600; }
    pre { background: #f3f4f6; padding: 10px; border-radius: 4px; overflow-x: auto; }
</style>";

// Test 1: Check database connection
echo "<div class='section'>";
echo "<h2>1. Database Connection</h2>";
try {
    DB::connection()->getPdo();
    echo "<p class='success'>✅ Database connection successful</p>";
} catch (\Exception $e) {
    echo "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}
echo "</div>";

// Test 2: Check tables exist
echo "<div class='section'>";
echo "<h2>2. Database Tables</h2>";
$tables = ['restaurant_tables', 'table_reservations', 'table_orders'];
foreach ($tables as $table) {
    try {
        DB::table($table)->count();
        echo "<p class='success'>✅ Table '{$table}' exists</p>";
    } catch (\Exception $e) {
        echo "<p class='error'>❌ Table '{$table}' missing</p>";
    }
}
echo "</div>";

// Test 3: Count records
echo "<div class='section'>";
echo "<h2>3. Records Count</h2>";
try {
    $tablesCount = RestaurantTable::count();
    $reservationsCount = TableReservation::count();
    $ordersCount = TableOrder::count();
    
    echo "<table>";
    echo "<tr><th>Table</th><th>Count</th></tr>";
    echo "<tr><td>Restaurant Tables</td><td class='info'>{$tablesCount}</td></tr>";
    echo "<tr><td>Reservations</td><td class='info'>{$reservationsCount}</td></tr>";
    echo "<tr><td>Orders</td><td class='info'>{$ordersCount}</td></tr>";
    echo "</table>";
} catch (\Exception $e) {
    echo "<p class='error'>❌ Error counting records: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 4: Show sample tables
echo "<div class='section'>";
echo "<h2>4. Sample Tables (First 5)</h2>";
try {
    $sampleTables = RestaurantTable::take(5)->get();
    
    if ($sampleTables->count() > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Type</th><th>Chairs</th><th>Status</th><th>Custom</th></tr>";
        foreach ($sampleTables as $table) {
            $isCustom = $table->is_custom ? 'Yes' : 'No';
            echo "<tr>";
            echo "<td>{$table->id}</td>";
            echo "<td>{$table->table_name}</td>";
            echo "<td>{$table->table_type}</td>";
            echo "<td>{$table->chair_count}</td>";
            echo "<td>{$table->status}</td>";
            echo "<td>{$isCustom}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='info'>ℹ️ No tables found in database</p>";
    }
} catch (\Exception $e) {
    echo "<p class='error'>❌ Error fetching tables: " . $e->getMessage() . "</p>";
}
echo "</div>";

// Test 5: API Routes
echo "<div class='section'>";
echo "<h2>5. API Endpoints</h2>";
echo "<p class='info'>Test these endpoints in your browser console:</p>";
echo "<pre>";
echo "// Get all tables\n";
echo "fetch('/business/tables', {\n";
echo "    headers: { 'Accept': 'application/json' }\n";
echo "}).then(r => r.json()).then(console.log)\n\n";

echo "// Create table (requires authentication)\n";
echo "fetch('/business/tables', {\n";
echo "    method: 'POST',\n";
echo "    headers: {\n";
echo "        'Content-Type': 'application/json',\n";
echo "        'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').content\n";
echo "    },\n";
echo "    body: JSON.stringify({\n";
echo "        table_name: 'Test Table',\n";
echo "        table_type: 'round',\n";
echo "        chair_count: 4\n";
echo "    })\n";
echo "}).then(r => r.json()).then(console.log)\n";
echo "</pre>";
echo "</div>";

// Test 6: JavaScript Integration Check
echo "<div class='section'>";
echo "<h2>6. JavaScript Integration</h2>";
echo "<p class='info'>Open browser console (F12) and run:</p>";
echo "<pre>";
echo "// Check if functions are loaded\n";
echo "console.log('getTablesFromBackend:', typeof getTablesFromBackend);\n";
echo "console.log('createTableInBackend:', typeof createTableInBackend);\n";
echo "console.log('updateTablePosition:', typeof updateTablePosition);\n\n";

echo "// Test fetching tables\n";
echo "getTablesFromBackend().then(tables => {\n";
echo "    console.log('Tables from backend:', tables);\n";
echo "});\n";
echo "</pre>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>✅ Test Complete</h2>";
echo "<p>If all checks passed, your backend integration is working correctly!</p>";
echo "<p><strong>Next steps:</strong></p>";
echo "<ol>";
echo "<li>Go to POS page: <a href='/business/sales/create'>/business/sales/create</a></li>";
echo "<li>Click on 'Tables' tab</li>";
echo "<li>Open browser console (F12)</li>";
echo "<li>Watch for console logs showing database operations</li>";
echo "<li>Try dragging a table - you should see position save logs</li>";
echo "<li>Try rotating a table - you should see rotation save logs</li>";
echo "</ol>";
echo "</div>";
