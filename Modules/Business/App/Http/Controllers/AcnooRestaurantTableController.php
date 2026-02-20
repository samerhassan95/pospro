<?php
namespace Modules\Business\App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Auth;

class AcnooRestaurantTableController extends Controller {
    public function index() {
        $tables = RestaurantTable::where('business_id', Auth::user()->business_id)
            ->active()
            ->get();
        
        // Add aliases in the response array, not in the model
        $tablesArray = $tables->map(function($table) {
            return [
                'id' => $table->id,
                'business_id' => $table->business_id,
                'table_name' => $table->table_name,
                'name' => $table->table_name, // Alias for frontend
                'table_type' => $table->table_type,
                'chair_count' => $table->chair_count,
                'chairs' => $table->chair_count, // Alias for frontend
                'status' => $table->status,
                'is_custom' => $table->is_custom,
                'is_active' => $table->is_active,
                'position_top' => $table->position_top,
                'position_left' => $table->position_left,
                'position_right' => $table->position_right,
                'position_bottom' => $table->position_bottom,
                'rotation' => $table->rotation,
                'created_at' => $table->created_at,
                'updated_at' => $table->updated_at,
            ];
        });
        
        return response()->json(['success' => true, 'data' => $tablesArray]);
    }
    
    public function store(Request $request) {
        $data = $request->validate([
            'table_name' => 'required|string|max:50',
            'table_type' => 'required',
            'chair_count' => 'required|integer',
            'position_top' => 'nullable|string',
            'position_left' => 'nullable|string'
        ]);
        
        // Check if table name already exists for this business
        $existingTable = RestaurantTable::where('business_id', Auth::user()->business_id)
            ->where('table_name', $data['table_name'])
            ->first();
            
        if ($existingTable) {
            return response()->json([
                'success' => false, 
                'message' => 'Table name already exists. Please choose a different name.'
            ], 422);
        }
        
        $data['business_id'] = Auth::user()->business_id;
        $data['is_custom'] = true;
        $data['status'] = 'free';
        $data['is_active'] = true;
        
        $table = RestaurantTable::create($data);
        return response()->json(['success' => true, 'data' => $table, 'message' => 'Table added successfully!'], 201);
    }
    
    public function update(Request $request, RestaurantTable $table) {
        if ($table->business_id !== Auth::user()->business_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $table->update($request->all());
        return response()->json(['success' => true, 'data' => $table]);
    }
    
    public function updatePosition(Request $request, RestaurantTable $table) {
        if ($table->business_id !== Auth::user()->business_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $data = $request->validate([
            'position_top' => 'nullable|string',
            'position_left' => 'nullable|string',
            'position_right' => 'nullable|string',
            'position_bottom' => 'nullable|string',
            'rotation' => 'nullable|integer'
        ]);
        
        $table->update($data);
        return response()->json(['success' => true, 'data' => $table]);
    }
    
    public function rotate(Request $request, RestaurantTable $table) {
        if ($table->business_id !== Auth::user()->business_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $degrees = $request->input('degrees', 90);
        $newRotation = ($table->rotation + $degrees) % 360;
        $table->update(['rotation' => $newRotation]);
        
        return response()->json(['success' => true, 'data' => $table]);
    }
    
    public function destroy(RestaurantTable $table) {
        if ($table->business_id !== Auth::user()->business_id || !$table->is_custom) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        $table->delete();
        return response()->json(['success' => true]);
    }
}