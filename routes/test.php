<?php

use Illuminate\Support\Facades\Route;
use App\Models\Option;

// Test route to manually set colors
Route::get('/test-set-colors', function() {
    $opt = Option::where('key', 'general')->first();
    $val = $opt->value;
    
    // Set test colors
    $val['primary_color'] = '#ff0000'; // RED
    $val['secondary_color'] = '#00ff00'; // GREEN
    
    $opt->value = $val;
    $opt->save();
    
    // Clear cache
    \Illuminate\Support\Facades\Cache::forget('general');
    
    return response()->json([
        'message' => 'Colors set to RED and GREEN',
        'primary' => get_primary_color(),
        'secondary' => get_secondary_color(),
        'instruction' => 'Now refresh your browser with Ctrl+Shift+R to see the changes'
    ]);
});

// Test route to check current colors
Route::get('/test-check-colors', function() {
    $opt = Option::where('key', 'general')->first();
    
    return response()->json([
        'database' => [
            'primary_color' => $opt->value['primary_color'] ?? 'NOT SET',
            'secondary_color' => $opt->value['secondary_color'] ?? 'NOT SET',
        ],
        'helpers' => [
            'get_primary_color()' => get_primary_color(),
            'get_secondary_color()' => get_secondary_color(),
        ]
    ]);
});

// Test route to reset colors
Route::get('/test-reset-colors', function() {
    $opt = Option::where('key', 'general')->first();
    $val = $opt->value;
    
    $val['primary_color'] = '#011646';
    $val['secondary_color'] = '#0071bc';
    
    $opt->value = $val;
    $opt->save();
    
    \Illuminate\Support\Facades\Cache::forget('general');
    
    return response()->json([
        'message' => 'Colors reset to defaults',
        'primary' => get_primary_color(),
        'secondary' => get_secondary_color(),
    ]);
});
