<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model {
    protected $fillable = ['business_id', 'table_name', 'table_type', 'chair_count', 'position_top', 'position_left', 'position_right', 'position_bottom', 'rotation', 'status', 'is_custom', 'is_active'];
    protected $casts = ['is_custom' => 'boolean', 'is_active' => 'boolean', 'rotation' => 'integer'];
    
    public function business() { return $this->belongsTo(Business::class); }
    public function scopeActive($query) { return $query->where('is_active', true); }
    public function updateStatus($status) { return $this->update(['status' => $status]); }
}