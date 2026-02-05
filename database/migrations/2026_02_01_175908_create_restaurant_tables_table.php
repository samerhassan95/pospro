<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('table_name', 50);
            $table->enum('table_type', ['circle', 'rounded', 'rectangle', 'rectangle-h', 'rectangle-h10'])->default('circle');
            $table->integer('chair_count')->default(4);
            $table->string('position_top', 20)->nullable();
            $table->string('position_left', 20)->nullable();
            $table->string('position_right', 20)->nullable();
            $table->string('position_bottom', 20)->nullable();
            $table->integer('rotation')->default(0); // NEW: 0, 90, 180, 270 degrees
            $table->enum('status', ['free', 'utilized', 'blocked'])->default('free');
            $table->boolean('is_custom')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->unique(['business_id', 'table_name']);
        });
    }
    public function down() { Schema::dropIfExists('restaurant_tables'); }
};