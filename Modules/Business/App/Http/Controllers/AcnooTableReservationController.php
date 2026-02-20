<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TableReservation;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcnooTableReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = TableReservation::where('business_id', Auth::user()->business_id)
            ->with(['table' => function($query) {
                $query->select('id', 'table_name', 'status');
            }])
            ->orderBy('reservation_date', 'desc')
            ->orderBy('reservation_time', 'desc')
            ->get()
            ->map(function ($reservation) {
                // Ensure table_name is available even if table relation is not loaded or null
                $reservation->table_name = $reservation->table ? $reservation->table->table_name : 'Unknown Table';
                return $reservation;
            });

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log raw request for debugging
            Log::info('Reservation Store Request:', $request->all());

            // Validate both JS naming conventions and DB columns to be safe
            $validated = $request->validate([
                'table_id' => 'required|exists:restaurant_tables,id',
                'customer_name' => 'required|string',
                'phone' => 'nullable|string',
                'customer_phone' => 'nullable|string', // Allow both
                'reservation_date' => 'required|date',
                'reservation_time' => 'required',
                'guest_count' => 'nullable|integer',
                'number_of_guests' => 'nullable|integer', // Allow both
                'notes' => 'nullable|string',
                'special_notes' => 'nullable|string', // Allow both
            ]);

            $businessId = Auth::user()->business_id;

            // Normalize data maps
            $data = [
                'business_id' => $businessId,
                'table_id' => $validated['table_id'],
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $request->phone ?? $request->customer_phone,
                'reservation_date' => $validated['reservation_date'],
                'reservation_time' => $validated['reservation_time'],
                'number_of_guests' => $request->guest_count ?? $request->number_of_guests ?? 1,
                'special_notes' => $request->notes ?? $request->special_notes,
                'status' => 'reserved' // Changed from 'pending' to 'reserved'
            ];

            // Check for overlap
            $exists = TableReservation::where('table_id', $data['table_id'])
                ->where('reservation_date', $data['reservation_date'])
                ->where('status', 'reserved') // Changed from 'pending' to 'reserved'
                ->where(function ($query) use ($data) {
                    // Simple check: if within 2 hours of another reservation
                    // This can be more complex based on requirements
                    $time = strtotime($data['reservation_time']);
                    $start = date('H:i:s', $time - 7200); // -2 hours
                    $end = date('H:i:s', $time + 7200); // +2 hours
                    $query->whereBetween('reservation_time', [$start, $end]);
                })
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'This table is already reserved for the selected time slot (overlapping reservation).'
                ], 422);
            }

            DB::beginTransaction();

            $reservation = TableReservation::create($data);

            // Update table status to blocked
            $table = RestaurantTable::find($data['table_id']);
            if ($table) {
                $table->status = 'blocked';
                $table->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation created successfully',
                'data' => $reservation
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Reservation Create Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error creating reservation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark reservation as guest arrived.
     */
    public function guestArrived($id)
    {
        try {
            $reservation = TableReservation::findOrFail($id);
            
            DB::beginTransaction();
            
            $reservation->status = 'completed'; // Or 'arrived' if that enum exists
            $reservation->save();
            
            // Mark table as utilized
            $table = RestaurantTable::find($reservation->table_id);
            if ($table) {
                $table->status = 'utilized';
                $table->save();
            }
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Guest arrived']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Mark reservation as complete.
     */
    public function complete($id)
    {
        // Alias for guestArrived or explicit complete logic
        return $this->guestArrived($id);
    }

    /**
     * Cancel a reservation.
     */
    public function cancel($id)
    {
        try {
            $reservation = TableReservation::findOrFail($id);
            
            DB::beginTransaction();
            
            $reservation->status = 'cancelled';
            $reservation->save();
            
            // Free the table if it was blocked by this reservation
            // Note: In a real app, we should check if there are other pending reservations for today/now
            $table = RestaurantTable::find($reservation->table_id);
            if ($table && $table->status == 'blocked') {
                $table->status = 'free';
                $table->save();
            }
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Reservation cancelled']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        return $this->cancel($id);
    }
}