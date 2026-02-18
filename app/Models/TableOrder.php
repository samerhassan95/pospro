<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TableOrder extends Model {
    protected $fillable = ['business_id', 'table_id', 'sale_id', 'customer_name', 'number_of_guests', 'order_items', 'special_notes', 'order_time', 'status'];
    
    public function table() { return $this->belongsTo(RestaurantTable::class, 'table_id'); }
    public function complete() { return $this->update(['status' => 'completed']); }
}