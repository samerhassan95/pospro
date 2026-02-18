<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TableReservation extends Model {
    protected $fillable = ['business_id', 'table_id', 'customer_name', 'customer_phone', 'reservation_date', 'reservation_time', 'number_of_guests', 'special_notes', 'status', 'time_arrived'];
    protected $casts = ['reservation_date' => 'date', 'time_arrived' => 'boolean'];
    
    public function table() { return $this->belongsTo(RestaurantTable::class, 'table_id'); }
    public function markAsArrived() { return $this->update(['status' => 'arrived', 'time_arrived' => true]); }
    public function cancel() { return $this->update(['status' => 'cancelled']); }
}