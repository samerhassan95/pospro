<?php
namespace Modules\Business\App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\FloorPlanLayout;
use Illuminate\Http\Request;
use Auth;

class AcnooFloorPlanLayoutController extends Controller {
    
    // List all layouts for current business
    public function index() {
        $layouts = FloorPlanLayout::where('business_id', Auth::user()->business_id)
            ->orderBy('is_default', 'desc')
            ->orderBy('is_active', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true, 
            'data' => $layouts
        ]);
    }
    
    // Get active layout
    public function getActive() {
        $layout = FloorPlanLayout::where('business_id', Auth::user()->business_id)
            ->active()
            ->first();
        
        return response()->json([
            'success' => true, 
            'data' => $layout
        ]);
    }
    
    // Get default layout
    public function getDefault() {
        $layout = FloorPlanLayout::where('business_id', Auth::user()->business_id)
            ->default()
            ->first();
        
        return response()->json([
            'success' => true, 
            'data' => $layout
        ]);
    }
    
    // Save current floor plan as new layout
    public function store(Request $request) {
        $data = $request->validate([
            'layout_name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'entrance_position' => 'nullable|array',
            'area_positions' => 'nullable|array',
            'table_positions' => 'nullable|array',
            'set_as_default' => 'nullable|boolean'
        ]);
        
        $layout = FloorPlanLayout::create([
            'business_id' => Auth::user()->business_id,
            'layout_name' => $data['layout_name'],
            'description' => $data['description'] ?? null,
            'entrance_position' => $data['entrance_position'] ?? null,
            'area_positions' => $data['area_positions'] ?? null,
            'table_positions' => $data['table_positions'] ?? null,
            'is_active' => false,
            'is_default' => false
        ]);
        
        // Set as default if requested
        if ($request->input('set_as_default', false)) {
            $layout->setAsDefault();
        }
        
        return response()->json([
            'success' => true, 
            'message' => __('Floor plan layout saved successfully'),
            'data' => $layout
        ], 201);
    }
    
    // Update existing layout
    public function update(Request $request, FloorPlanLayout $layout) {
        if ($layout->business_id !== Auth::user()->business_id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }
        
        $data = $request->validate([
            'layout_name' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'entrance_position' => 'nullable|array',
            'area_positions' => 'nullable|array',
            'table_positions' => 'nullable|array'
        ]);
        
        $layout->update($data);
        
        return response()->json([
            'success' => true, 
            'message' => __('Layout updated successfully'),
            'data' => $layout
        ]);
    }
    
    // Apply/activate a layout
    public function activate(FloorPlanLayout $layout) {
        if ($layout->business_id !== Auth::user()->business_id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }
        
        $positions = $layout->apply();
        
        return response()->json([
            'success' => true, 
            'message' => __('Layout activated successfully'),
            'data' => $positions
        ]);
    }
    
    // Set layout as default
    public function setDefault(FloorPlanLayout $layout) {
        if ($layout->business_id !== Auth::user()->business_id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }
        
        $layout->setAsDefault();
        
        return response()->json([
            'success' => true, 
            'message' => __('Layout set as default successfully')
        ]);
    }
    
    // Delete layout
    public function destroy(FloorPlanLayout $layout) {
        if ($layout->business_id !== Auth::user()->business_id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }
        
        // Don't allow deleting active layout
        if ($layout->is_active) {
            return response()->json([
                'success' => false, 
                'message' => __('Cannot delete active layout. Please activate another layout first.')
            ], 400);
        }
        
        $layout->delete();
        
        return response()->json([
            'success' => true, 
            'message' => __('Layout deleted successfully')
        ]);
    }
    
    // Duplicate layout
    public function duplicate(FloorPlanLayout $layout) {
        if ($layout->business_id !== Auth::user()->business_id) {
            return response()->json(['success' => false, 'message' => __('Unauthorized')], 403);
        }
        
        $newLayout = $layout->replicate();
        $newLayout->layout_name = $layout->layout_name . ' (Copy)';
        $newLayout->is_active = false;
        $newLayout->is_default = false;
        $newLayout->save();
        
        return response()->json([
            'success' => true, 
            'message' => __('Layout duplicated successfully'),
            'data' => $newLayout
        ], 201);
    }
}