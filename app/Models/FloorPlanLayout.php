<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class FloorPlanLayout extends Model {
    protected $fillable = [
        'business_id', 'layout_name', 'description', 
        'entrance_position', 'area_positions', 'table_positions',
        'is_active', 'is_default'
    ];
    
    protected $casts = [
        'entrance_position' => 'array',
        'area_positions' => 'array',
        'table_positions' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];
    
    public function business() { 
        return $this->belongsTo(Business::class); 
    }
    
    // Set this layout as active (deactivate others)
    public function activate() {
        \DB::transaction(function() {
            // Deactivate all other layouts for this business
            self::where('business_id', $this->business_id)
                ->where('id', '!=', $this->id)
                ->update(['is_active' => false]);
            
            // Activate this layout
            $this->update(['is_active' => true]);
        });
        return $this;
    }
    
    // Set as default layout
    public function setAsDefault() {
        \DB::transaction(function() {
            self::where('business_id', $this->business_id)
                ->where('id', '!=', $this->id)
                ->update(['is_default' => false]);
            
            $this->update(['is_default' => true]);
        });
        return $this;
    }
    
    // Capture current floor plan state
    public static function captureCurrentLayout($businessId, $layoutName, $description = null) {
        // Get current positions from localStorage (passed from frontend)
        return self::create([
            'business_id' => $businessId,
            'layout_name' => $layoutName,
            'description' => $description,
            'entrance_position' => request('entrance_position'),
            'area_positions' => request('area_positions'),
            'table_positions' => request('table_positions'),
            'is_active' => false,
            'is_default' => false
        ]);
    }
    
    // Apply this layout (returns positions to set in frontend)
    public function apply() {
        $this->activate();
        
        return [
            'entrance_position' => $this->entrance_position,
            'area_positions' => $this->area_positions,
            'table_positions' => $this->table_positions
        ];
    }
    
    // Scope for active layout
    public function scopeActive($query) {
        return $query->where('is_active', true);
    }
    
    // Scope for default layout
    public function scopeDefault($query) {
        return $query->where('is_default', true);
    }
}