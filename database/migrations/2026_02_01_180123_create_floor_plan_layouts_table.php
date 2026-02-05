<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('floor_plan_layouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('layout_name', 100);
            $table->text('description')->nullable();
            $table->json('entrance_position')->nullable(); // {top, left, right, bottom, side}
            $table->json('area_positions')->nullable(); // {bar-area: {}, toilets: {}, center-square: {}}
            $table->json('table_positions')->nullable(); // {Ta1: {}, Ta2: {}, ...}
            $table->boolean('is_active')->default(false); // Currently active layout
            $table->boolean('is_default')->default(false); // Default layout for new sessions
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'is_active']);
        });
    }
    public function down() { Schema::dropIfExists('floor_plan_layouts'); }
};