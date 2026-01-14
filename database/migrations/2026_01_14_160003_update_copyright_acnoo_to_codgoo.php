<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Option;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $option = Option::where('key', 'general')->first();
        
        if ($option) {
            $value = $option->value;
            $value['copy_right'] = '© 2026 codgoo, all rights reserved.';
            $option->value = $value;
            $option->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $option = Option::where('key', 'general')->first();
        
        if ($option) {
            $value = $option->value;
            $value['copy_right'] = '© 2026 Acnoo, all rights reserved.';
            $option->value = $value;
            $option->save();
        }
    }
};
