<?php
/**
 * Clear All Caches Script
 * Run this after updating currency symbol to SVG
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧹 Clearing all caches...\n\n";

// Clear application cache
echo "1. Clearing application cache...\n";
Artisan::call('cache:clear');
echo "   ✅ Application cache cleared\n\n";

// Clear config cache
echo "2. Clearing config cache...\n";
Artisan::call('config:clear');
echo "   ✅ Config cache cleared\n\n";

// Clear route cache
echo "3. Clearing route cache...\n";
Artisan::call('route:clear');
echo "   ✅ Route cache cleared\n\n";

// Clear view cache
echo "4. Clearing view cache...\n";
Artisan::call('view:clear');
echo "   ✅ View cache cleared\n\n";

// Clear compiled classes
echo "5. Clearing compiled classes...\n";
Artisan::call('clear-compiled');
echo "   ✅ Compiled classes cleared\n\n";

// Optimize autoloader
echo "6. Optimizing autoloader...\n";
Artisan::call('optimize:clear');
echo "   ✅ Optimization cache cleared\n\n";

echo "✅ All caches cleared successfully!\n\n";
echo "📝 Next steps:\n";
echo "   1. Clear your browser cache (Ctrl+Shift+Delete)\n";
echo "   2. Or open the site in incognito/private mode\n";
echo "   3. Test the SAR currency symbol in:\n";
echo "      - Dashboard\n";
echo "      - POS\n";
echo "      - Sales\n";
echo "      - Reports\n";
echo "      - Invoices\n\n";
echo "🎉 The SAR symbol should now appear as SVG icon!\n";
