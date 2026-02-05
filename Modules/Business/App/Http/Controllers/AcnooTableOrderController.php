<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TableOrder;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcnooTableOrderController extends Controller
{
    public function index()
    {
        $orders = TableOrder::where('business_id', Auth::user()->business_id)
            ->where('status', '!=', 'completed') // Show all active orders
            ->with(['table' => function($query) {
                $query->select('id', 'name');
            }])
            ->latest()
            ->get()
            ->map(function ($order) {
                $order->table_name = $order->table ? $order->table->name : 'Unknown';
                // Map back to JS expected fields if needed, or JS accepts snake_case
                return $order;
            });
            
        return response()->json(['success' => true, 'data' => $orders]);
    }

    public function store(Request $request)
    {
        try {
            Log::info('Order Store Request:', $request->all());

            $validated = $request->validate([
                'table_id' => 'required',
                'customer_name' => 'nullable|string',
                'guest_count' => 'nullable|integer',
                'number_of_guests' => 'nullable|integer',
                'items' => 'nullable|string',
                'order_items' => 'nullable|string',
                'notes' => 'nullable|string',
                'special_notes' => 'nullable|string',
            ]);

            $businessId = Auth::user()->business_id;

            $data = [
                'business_id' => $businessId,
                'table_id' => $validated['table_id'],
                'customer_name' => $validated['customer_name'] ?? 'Guest',
                'number_of_guests' => $request->guest_count ?? $request->number_of_guests ?? 1,
                'order_items' => $request->items ?? $request->order_items,
                'special_notes' => $request->notes ?? $request->special_notes,
                'status' => 'in_progress', // Default status
                'order_time' => now()
            ];

            // Check if there is already an active order for this table
            $existingOrder = TableOrder::where('table_id', $data['table_id'])
                ->where('status', 'in_progress')
                ->first();

            DB::beginTransaction();

            if ($existingOrder) {
                // Update existing order
                $existingOrder->update($data);
                $order = $existingOrder;
            } else {
                // Create new order
                $order = TableOrder::create($data);
            }

            // Update table status
            $table = RestaurantTable::find($data['table_id']);
            if ($table) {
                $table->status = 'utilized';
                $table->save();
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Order saved successfully',
                'data' => $order
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Create Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error saving order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function complete($id)
    {
        try {
            $order = TableOrder::findOrFail($id);
            
            DB::beginTransaction();
            
            $order->status = 'completed';
            $order->save();
            
            // Free the table
            $table = RestaurantTable::find($order->table_id);
            if ($table) {
                $table->status = 'free';
                $table->save();
            }
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Order completed']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    public function show($id)
    {
        return response()->json(['success' => true, 'data' => TableOrder::with('table')->find($id)]);
    }
}