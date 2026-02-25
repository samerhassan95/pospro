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
            $value['admin_footer_link_text'] = 'Nomu';
            $value['admin_footer_link'] = 'https://codgoo.com/';
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
            $value['admin_footer_link_text'] = 'Nomu';
            $value['admin_footer_link'] = 'https://acnoo.com/';
            $option->value = $value;
            $option->save();
        }
    }
};
