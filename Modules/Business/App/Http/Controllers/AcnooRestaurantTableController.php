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
        
        return response()->json(['success' => true, 'data' => $tables]);
    }
    
    public function store(Request $request) {
        $data = $request->validate([
            'table_name' => 'required|string|max:50',
            'table_type' => 'required',
            'chair_count' => 'required|integer',
            'position_top' => 'nullable|string',
            'position_left' => 'nullable|string'
        ]);
        
        $data['business_id'] = Auth::user()->business_id;
        $data['is_custom'] = true;
        
        $table = RestaurantTable::create($data);
        return response()->json(['success' => true, 'data' => $table], 201);
    }
}