<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('table_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('table_id');
            $table->string('customer_name');
            $table->string('customer_phone', 20)->nullable();
            $table->date('reservation_date');
            $table->time('reservation_time');
            $table->integer('number_of_guests');
            $table->text('special_notes')->nullable();
            $table->enum('status', ['reserved', 'arrived', 'cancelled', 'completed'])->default('reserved');
            $table->boolean('time_arrived')->default(false);
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('restaurant_tables')->onDelete('cascade');
        });
    }
    public function down() { Schema::dropIfExists('table_reservations'); }
};