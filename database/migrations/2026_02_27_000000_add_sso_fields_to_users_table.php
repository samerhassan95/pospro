<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('external_id')->nullable()->after('id')->comment('User ID from master app');
            $table->string('sso_provider')->nullable()->after('external_id')->default('nomuapps');
            $table->timestamp('last_sso_login')->nullable()->after('sso_provider');
            
            $table->index(['external_id', 'sso_provider'], 'idx_sso_lookup');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_sso_lookup');
            $table->dropColumn(['external_id', 'sso_provider', 'last_sso_login']);
        });
    }
};
