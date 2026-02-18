<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('table_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('table_id');
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->integer('number_of_guests')->default(1);
            $table->text('order_items')->nullable();
            $table->text('special_notes')->nullable();
            $table->time('order_time')->nullable();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('restaurant_tables')->onDelete('cascade');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
        });
    }
    public function down() { Schema::dropIfExists('table_orders'); }
};