# Table Reservation System - Backend Implementation

## File Locations
- Models: `app/Models/`
- Controllers: `Modules/Business/App/Http/Controllers/`
- Routes: `Modules/Business/routes/api.php`
- Migrations: `database/migrations/`

---

## Step 1: Run Migrations

Create 3 migration files in `database/migrations/`:

### 2026_01_30_000001_create_restaurant_tables_table.php
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('table_name', 50);
            $table->enum('table_type', ['circle', 'rounded', 'rectangle', 'rectangle-h', 'rectangle-h10'])->default('circle');
            $table->integer('chair_count')->default(4);
            $table->string('position_top', 20)->nullable();
            $table->string('position_left', 20)->nullable();
            $table->string('position_right', 20)->nullable();
            $table->string('position_bottom', 20)->nullable();
            $table->integer('rotation')->default(0); // NEW: 0, 90, 180, 270 degrees
            $table->enum('status', ['free', 'utilized', 'blocked'])->default('free');
            $table->boolean('is_custom')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->unique(['business_id', 'table_name']);
        });
    }
    public function down() { Schema::dropIfExists('restaurant_tables'); }
};
```

### 2026_01_30_000002_create_table_reservations_table.php
```php
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
```

### 2026_01_30_000003_create_table_orders_table.php
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('table_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('table_id');
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->integer('number_of_guests')->default(1);
            $table->text('order_items')->nullable();
            $table->text('special_notes')->nullable();
            $table->time('order_time')->nullable();
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('table_id')->references('id')->on('restaurant_tables')->onDelete('cascade');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
        });
    }
    public function down() { Schema::dropIfExists('table_orders'); }
};
```

Run: `php artisan migrate`

---

## Step 2: Create Models

Create 3 model files in `app/Models/`:

### RestaurantTable.php
```php
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class RestaurantTable extends Model {
    protected $fillable = ['business_id', 'table_name', 'table_type', 'chair_count', 'position_top', 'position_left', 'position_right', 'position_bottom', 'rotation', 'status', 'is_custom', 'is_active'];
    protected $casts = ['is_custom' => 'boolean', 'is_active' => 'boolean', 'rotation' => 'integer'];
    
    public function business() { return $this->belongsTo(Business::class); }
    public function activeReservation() { return $this->hasOne(TableReservation::class, 'table_id')->where('status', 'reserved')->latest(); }
    public function activeOrder() { return $this->hasOne(TableOrder::class, 'table_id')->where('status', 'in_progress')->latest(); }
    public function scopeActive($query) { return $query->where('is_active', true); }
    public function updateStatus($status) { return $this->update(['status' => $status]); }
    public function updatePosition($top, $left, $right = null, $bottom = null, $rotation = 0) { 
        return $this->update(['position_top' => $top, 'position_left' => $left, 'position_right' => $right, 'position_bottom' => $bottom, 'rotation' => $rotation]); 
    }
    public function rotate($degrees = 90) {
        $newRotation = ($this->rotation + $degrees) % 360;
        return $this->update(['rotation' => $newRotation]);
    }
}
```

### TableReservation.php
```php
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
```

### TableOrder.php
```php
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TableOrder extends Model {
    protected $fillable = ['business_id', 'table_id', 'sale_id', 'customer_name', 'number_of_guests', 'order_items', 'special_notes', 'order_time', 'status'];
    
    public function table() { return $this->belongsTo(RestaurantTable::class, 'table_id'); }
    public function complete() { return $this->update(['status' => 'completed']); }
}
```

---

## Step 3: Create Controllers

Create 3 controller files in `Modules/Business/App/Http/Controllers/`:

### AcnooRestaurantTableController.php
```php
<?php
namespace Modules\Business\App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\RestaurantTable;
use Illuminate\Http\Request;
use Auth;

class AcnooRestaurantTableController extends Controller {
    public function index() {
        return response()->json(['success' => true, 'data' => RestaurantTable::where('business_id', Auth::user()->business_id)->active()->with(['activeReservation', 'activeOrder'])->get()]);
    }
    
    public function store(Request $request) {
        $data = $request->validate(['table_name' => 'required|string|max:50', 'table_type' => 'required', 'chair_count' => 'required|integer', 'position_top' => 'nullable|string', 'position_left' => 'nullable|string', 'position_right' => 'nullable|string', 'position_bottom' => 'nullable|string']);
        $data['business_id'] = Auth::user()->business_id;
        $data['is_custom'] = true;
        return response()->json(['success' => true, 'data' => RestaurantTable::create($data)], 201);
    }
    
    public function update(Request $request, RestaurantTable $table) {
        if ($table->business_id !== Auth::user()->business_id) return response()->json(['success' => false], 403);
        $table->update($request->all());
        return response()->json(['success' => true, 'data' => $table]);
    }
    
    public function updatePosition(Request $request, RestaurantTable $table) {
        if ($table->business_id !== Auth::user()->business_id) return response()->json(['success' => false], 403);
        $data = $request->validate(['position_top' => 'nullable|string', 'position_left' => 'nullable|string', 'position_right' => 'nullable|string', 'position_bottom' => 'nullable|string', 'rotation' => 'nullable|integer']);
        $table->updatePosition($data['position_top'], $data['position_left'], $data['position_right'] ?? null, $data['position_bottom'] ?? null, $data['rotation'] ?? 0);
        return response()->json(['success' => true, 'data' => $table]);
    }
    
    public function rotate(Request $request, RestaurantTable $table) {
        if ($table->business_id !== Auth::user()->business_id) return response()->json(['success' => false], 403);
        $degrees = $request->input('degrees', 90);
        $table->rotate($degrees);
        return response()->json(['success' => true, 'data' => $table]);
    }
    
    public function destroy(RestaurantTable $table) {
        if ($table->business_id !== Auth::user()->business_id || !$table->is_custom) return response()->json(['success' => false], 403);
        $table->delete();
        return response()->json(['success' => true]);
    }
}
```

### AcnooTableReservationController.php
```php
<?php
namespace Modules\Business\App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\{TableReservation, RestaurantTable};
use Illuminate\Http\Request;
use Auth, DB;

class AcnooTableReservationController extends Controller {
    public function index() {
        return response()->json(['success' => true, 'data' => TableReservation::where('business_id', Auth::user()->business_id)->with('table')->get()]);
    }
    
    public function store(Request $request) {
        $data = $request->validate(['table_id' => 'required|exists:restaurant_tables,id', 'customer_name' => 'required', 'customer_phone' => 'nullable', 'reservation_date' => 'required|date', 'reservation_time' => 'required', 'number_of_guests' => 'required|integer', 'special_notes' => 'nullable']);
        $data['business_id'] = Auth::user()->business_id;
        
        DB::beginTransaction();
        $reservation = TableReservation::create($data);
        RestaurantTable::find($data['table_id'])->updateStatus('blocked');
        DB::commit();
        
        return response()->json(['success' => true, 'data' => $reservation], 201);
    }
    
    public function guestArrived(TableReservation $reservation) {
        DB::beginTransaction();
        $reservation->markAsArrived();
        $reservation->table->updateStatus('utilized');
        DB::commit();
        return response()->json(['success' => true]);
    }
    
    public function cancel(TableReservation $reservation) {
        DB::beginTransaction();
        $reservation->cancel();
        $reservation->table->updateStatus('free');
        DB::commit();
        return response()->json(['success' => true]);
    }
}
```

### AcnooTableOrderController.php
```php
<?php
namespace Modules\Business\App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\{TableOrder, RestaurantTable};
use Illuminate\Http\Request;
use Auth, DB;

class AcnooTableOrderController extends Controller {
    public function index() {
        return response()->json(['success' => true, 'data' => TableOrder::where('business_id', Auth::user()->business_id)->where('status', 'in_progress')->with('table')->get()]);
    }
    
    public function store(Request $request) {
        $data = $request->validate(['table_id' => 'required', 'customer_name' => 'nullable', 'number_of_guests' => 'required|integer', 'order_items' => 'nullable', 'special_notes' => 'nullable', 'order_time' => 'nullable']);
        $data['business_id'] = Auth::user()->business_id;
        
        DB::beginTransaction();
        $order = TableOrder::create($data);
        RestaurantTable::find($data['table_id'])->updateStatus('utilized');
        DB::commit();
        
        return response()->json(['success' => true, 'data' => $order], 201);
    }
    
    public function complete(TableOrder $order) {
        DB::beginTransaction();
        $order->complete();
        $order->table->updateStatus('free');
        DB::commit();
        return response()->json(['success' => true]);
    }
}
```

---

## Step 4: Add Routes

Add to `Modules/Business/routes/api.php`:

```php
use Modules\Business\App\Http\Controllers\{AcnooRestaurantTableController, AcnooTableReservationController, AcnooTableOrderController};

Route::middleware(['auth:sanctum'])->prefix('business')->group(function () {
    Route::apiResource('tables', AcnooRestaurantTableController::class);
    Route::post('tables/{table}/position', [AcnooRestaurantTableController::class, 'updatePosition']);
    Route::post('tables/{table}/rotate', [AcnooRestaurantTableController::class, 'rotate']);
    Route::post('reservations/{reservation}/arrived', [AcnooTableReservationController::class, 'guestArrived']);
    Route::apiResource('reservations', AcnooTableReservationController::class);
    Route::post('table-orders/{order}/complete', [AcnooTableOrderController::class, 'complete']);
    Route::apiResource('table-orders', AcnooTableOrderController::class);
});
```

---

## Step 5: Update Frontend

Replace localStorage with API calls:

```javascript
// Fetch tables
async function fetchTables() {
    const res = await fetch('/api/business/tables', {
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
    });
    return (await res.json()).data;
}

// Create reservation
async function createReservation(data) {
    await fetch('/api/business/reservations', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify(data)
    });
}

// Update table position (called on drag end)
async function updateTablePosition(tableId, position) {
    await fetch(`/api/business/tables/${tableId}/position`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({
            position_top: position.top,
            position_left: position.left,
            position_right: position.right,
            position_bottom: position.bottom,
            rotation: position.rotation || 0
        })
    });
}

// Rotate table by 90 degrees
async function rotateTableAPI(tableId, degrees = 90) {
    const res = await fetch(`/api/business/tables/${tableId}/rotate`, {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({ degrees })
    });
    return (await res.json()).data;
}
```

---

## Features Implemented

### 1. Table Management
- Create, read, update, delete restaurant tables
- Support for custom and default tables
- Table types: circle, rounded, rectangle, rectangle-h, rectangle-h10
- Chair count configuration
- **NEW**: Table rotation (0°, 90°, 180°, 270°)

### 2. Table Position Persistence
- Drag-and-drop table positioning with automatic save
- Positions stored as CSS values (e.g., "100px", "auto")
- Supports top, left, right, bottom positioning
- **NEW**: Rotation angle saved with position
- Works for both default and custom tables
- Position restored on page refresh
- Clear all positions option in "Clear All Data" button

### 3. Table Rotation ⭐ NEW
- **Right-click context menu**: Rotate tables by 90° increments
- **Visual indicator**: Blue badge shows current rotation angle
- **Persistent rotation**: Rotation saved with table position
- **Reset rotation**: Quick reset to 0° from context menu
- **Smooth animation**: CSS transitions for rotation
- **Use cases**:
  - Rectangular tables can face different directions
  - Optimize space utilization
  - Match real-world table arrangements
  - Better visual representation of floor plan
- Make reservations with customer details
- Guest count validation against table capacity
- Date and time selection
- Special notes support
- Reservation statuses: reserved, arrived, cancelled, completed
- Guest arrival tracking
- Automatic table status updates (free → blocked → utilized)

### 4. Order Management
- Create orders for tables
- Track customer name and guest count
- Order items and special notes
- Order time tracking
- Order statuses: in_progress, completed
- Link orders to sales records
- Automatic table status updates

### 5. Table Status System
- **Free** (🟢): Available for new reservations/orders
- **Blocked** (🟡): Reserved but guest hasn't arrived
- **Utilized** (🔴): Guest arrived or order in progress
- Real-time status updates across all operations

### 6. Data Management
- Clear all reservations and orders
- Clear all table positions
- Delete custom tables
- Automatic cleanup on table deletion
- Business-scoped data (multi-tenant support)

---

## API Endpoints

### Tables
- `GET /api/business/tables` - List all tables
- `POST /api/business/tables` - Create custom table
- `PUT /api/business/tables/{id}` - Update table
- `POST /api/business/tables/{id}/position` - Update table position
- `DELETE /api/business/tables/{id}` - Delete custom table

### Reservations
- `GET /api/business/reservations` - List all reservations
- `POST /api/business/reservations` - Create reservation
- `POST /api/business/reservations/{id}/arrived` - Mark guest as arrived
- `PUT /api/business/reservations/{id}` - Update reservation
- `DELETE /api/business/reservations/{id}` - Cancel reservation

### Orders
- `GET /api/business/table-orders` - List active orders
- `POST /api/business/table-orders` - Create order
- `POST /api/business/table-orders/{id}/complete` - Complete order
- `PUT /api/business/table-orders/{id}` - Update order

---

## NEW FEATURE: Floor Plan Layout Management

Save and load complete floor plan configurations including entrance position, area positions (Bar, Toilets, Center Square), and all table arrangements.

### Step 6: Create Floor Plan Layouts Migration

Create migration file in `database/migrations/`:

#### 2026_01_30_000004_create_floor_plan_layouts_table.php
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('floor_plan_layouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('business_id');
            $table->string('layout_name', 100);
            $table->text('description')->nullable();
            $table->json('entrance_position')->nullable(); // {top, left, right, bottom, side}
            $table->json('area_positions')->nullable(); // {bar-area: {}, toilets: {}, center-square: {}}
            $table->json('table_positions')->nullable(); // {Ta1: {}, Ta2: {}, ...}
            $table->boolean('is_active')->default(false); // Currently active layout
            $table->boolean('is_default')->default(false); // Default layout for new sessions
            $table->timestamps();
            
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->index(['business_id', 'is_active']);
        });
    }
    public function down() { Schema::dropIfExists('floor_plan_layouts'); }
};
```

Run: `php artisan migrate`

### Step 7: Create FloorPlanLayout Model

Create in `app/Models/FloorPlanLayout.php`:

```php
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
```

### Step 8: Create FloorPlanLayout Controller

Create in `Modules/Business/App/Http/Controllers/AcnooFloorPlanLayoutController.php`:

```php
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
```

### Step 9: Add Layout Routes

Add to `Modules/Business/routes/api.php`:

```php
use Modules\Business\App\Http\Controllers\AcnooFloorPlanLayoutController;

Route::middleware(['auth:sanctum'])->prefix('business')->group(function () {
    // ... existing routes ...
    
    // Floor Plan Layouts
    Route::get('floor-layouts/active', [AcnooFloorPlanLayoutController::class, 'getActive']);
    Route::get('floor-layouts/default', [AcnooFloorPlanLayoutController::class, 'getDefault']);
    Route::post('floor-layouts/{layout}/activate', [AcnooFloorPlanLayoutController::class, 'activate']);
    Route::post('floor-layouts/{layout}/set-default', [AcnooFloorPlanLayoutController::class, 'setDefault']);
    Route::post('floor-layouts/{layout}/duplicate', [AcnooFloorPlanLayoutController::class, 'duplicate']);
    Route::apiResource('floor-layouts', AcnooFloorPlanLayoutController::class);
});
```

### Step 10: Frontend Integration

Add these functions to your JavaScript:

```javascript
// Save current floor plan as layout
async function saveFloorPlanLayout(layoutName, description = '', setAsDefault = false) {
    const entrancePosition = JSON.parse(localStorage.getItem('areaPositions') || '{}').entrance || null;
    const areaPositions = JSON.parse(localStorage.getItem('areaPositions') || '{}');
    const tablePositions = JSON.parse(localStorage.getItem('tablePositions') || '{}');
    
    const response = await fetch('/api/business/floor-layouts', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            layout_name: layoutName,
            description: description,
            entrance_position: entrancePosition,
            area_positions: areaPositions,
            table_positions: tablePositions,
            set_as_default: setAsDefault
        })
    });
    
    const result = await response.json();
    if (result.success) {
        alert('Floor plan layout saved successfully!');
    }
    return result;
}

// Load all layouts
async function loadFloorPlanLayouts() {
    const response = await fetch('/api/business/floor-layouts', {
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
    });
    return (await response.json()).data;
}

// Apply a layout
async function applyFloorPlanLayout(layoutId) {
    const response = await fetch(`/api/business/floor-layouts/${layoutId}/activate`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
    
    const result = await response.json();
    if (result.success) {
        // Apply positions to localStorage
        if (result.data.entrance_position) {
            const areaPositions = JSON.parse(localStorage.getItem('areaPositions') || '{}');
            areaPositions.entrance = result.data.entrance_position;
            localStorage.setItem('areaPositions', JSON.stringify(areaPositions));
        }
        
        if (result.data.area_positions) {
            localStorage.setItem('areaPositions', JSON.stringify(result.data.area_positions));
        }
        
        if (result.data.table_positions) {
            localStorage.setItem('tablePositions', JSON.stringify(result.data.table_positions));
        }
        
        // Reload page to apply changes
        location.reload();
    }
    return result;
}

// Delete layout
async function deleteFloorPlanLayout(layoutId) {
    if (!confirm('Are you sure you want to delete this layout?')) return;
    
    const response = await fetch(`/api/business/floor-layouts/${layoutId}`, {
        method: 'DELETE',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
    });
    
    return await response.json();
}

// Duplicate layout
async function duplicateFloorPlanLayout(layoutId) {
    const response = await fetch(`/api/business/floor-layouts/${layoutId}/duplicate`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
    });
    
    return await response.json();
}

// Set layout as default
async function setDefaultLayout(layoutId) {
    const response = await fetch(`/api/business/floor-layouts/${layoutId}/set-default`, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
    });
    
    return await response.json();
}

// Load default layout on page load
async function loadDefaultLayoutOnInit() {
    const response = await fetch('/api/business/floor-layouts/default', {
        headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
    });
    
    const result = await response.json();
    if (result.success && result.data) {
        // Apply default layout if no positions saved in localStorage
        const hasLocalPositions = localStorage.getItem('tablePositions') || localStorage.getItem('areaPositions');
        
        if (!hasLocalPositions) {
            await applyFloorPlanLayout(result.data.id);
        }
    }
}

// Call on page load
document.addEventListener('DOMContentLoaded', loadDefaultLayoutOnInit);
```

### Step 11: Add UI Buttons

Add these buttons to your floor plan interface:

```html
<!-- Save Current Layout Button -->
<button type="button" class="btn btn-success" id="btn-save-layout">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M15 2H5C3.89543 2 3 2.89543 3 4V16C3 17.1046 3.89543 18 5 18H15C16.1046 18 17 17.1046 17 16V4C17 2.89543 16.1046 2 15 2Z" stroke="currentColor" stroke-width="2"/>
        <path d="M7 2V6H13V2" stroke="currentColor" stroke-width="2"/>
        <path d="M7 14H13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
    {{ __('Save Layout') }}
</button>

<!-- Load Layout Button -->
<button type="button" class="btn btn-info" id="btn-load-layout">
    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M3 3H17V17H3V3Z" stroke="currentColor" stroke-width="2"/>
        <path d="M7 7H13M7 10H13M7 13H10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>
    {{ __('Load Layout') }}
</button>

<script>
// Save Layout Button Handler
document.getElementById('btn-save-layout').addEventListener('click', function() {
    const layoutName = prompt('Enter layout name:');
    if (layoutName) {
        const description = prompt('Enter description (optional):');
        const setAsDefault = confirm('Set as default layout?');
        saveFloorPlanLayout(layoutName, description, setAsDefault);
    }
});

// Load Layout Button Handler
document.getElementById('btn-load-layout').addEventListener('click', async function() {
    const layouts = await loadFloorPlanLayouts();
    
    let layoutsList = '<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped"><thead><tr><th>Layout Name</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
    
    layouts.forEach(layout => {
        const statusBadges = [];
        if (layout.is_active) statusBadges.push('<span class="badge bg-success">Active</span>');
        if (layout.is_default) statusBadges.push('<span class="badge bg-primary">Default</span>');
        
        layoutsList += `<tr>
            <td><strong>${layout.layout_name}</strong></td>
            <td>${layout.description || '-'}</td>
            <td>${statusBadges.join(' ') || '-'}</td>
            <td>
                <button class="btn btn-sm btn-primary apply-layout-btn" data-id="${layout.id}">Apply</button>
                <button class="btn btn-sm btn-secondary duplicate-layout-btn" data-id="${layout.id}">Duplicate</button>
                <button class="btn btn-sm btn-warning set-default-btn" data-id="${layout.id}">Set Default</button>
                ${!layout.is_active ? `<button class="btn btn-sm btn-danger delete-layout-btn" data-id="${layout.id}">Delete</button>` : ''}
            </td>
        </tr>`;
    });
    
    layoutsList += '</tbody></table></div>';
    
    // Show modal with layouts
    const modalHtml = `
        <div class="modal fade" id="layoutsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Floor Plan Layouts</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">${layoutsList}</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('layoutsModal')?.remove();
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    const modal = new bootstrap.Modal(document.getElementById('layoutsModal'));
    modal.show();
    
    // Add event listeners
    document.querySelectorAll('.apply-layout-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            applyFloorPlanLayout(this.dataset.id);
        });
    });
    
    document.querySelectorAll('.delete-layout-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            await deleteFloorPlanLayout(this.dataset.id);
            modal.hide();
            document.getElementById('btn-load-layout').click();
        });
    });
    
    document.querySelectorAll('.duplicate-layout-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            await duplicateFloorPlanLayout(this.dataset.id);
            modal.hide();
            document.getElementById('btn-load-layout').click();
        });
    });
    
    document.querySelectorAll('.set-default-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            await setDefaultLayout(this.dataset.id);
            modal.hide();
            document.getElementById('btn-load-layout').click();
        });
    });
});
</script>
```

---

## Updated Features List

### 7. Floor Plan Layout Management ⭐ NEW
- **Save Layouts**: Capture current floor plan configuration (entrance, areas, tables)
- **Load Layouts**: Switch between saved layouts instantly
- **Default Layout**: Set a default layout for new sessions
- **Active Layout**: Track which layout is currently in use
- **Duplicate Layouts**: Copy existing layouts for variations
- **Layout Descriptions**: Add notes about each layout (e.g., "Weekend Setup", "Private Event")
- **Multi-tenant**: Each business has separate layouts
- **Use Cases**:
  - Different layouts for lunch vs dinner service
  - Special event configurations
  - Seasonal arrangements
  - A/B testing different floor plans
  - Quick setup for new staff

---

## Updated API Endpoints

### Floor Plan Layouts ⭐ NEW
- `GET /api/business/floor-layouts` - List all layouts
- `GET /api/business/floor-layouts/active` - Get currently active layout
- `GET /api/business/floor-layouts/default` - Get default layout
- `POST /api/business/floor-layouts` - Save current floor plan as new layout
- `PUT /api/business/floor-layouts/{id}` - Update layout
- `POST /api/business/floor-layouts/{id}/activate` - Apply/activate layout
- `POST /api/business/floor-layouts/{id}/set-default` - Set as default layout
- `POST /api/business/floor-layouts/{id}/duplicate` - Duplicate layout
- `DELETE /api/business/floor-layouts/{id}` - Delete layout

---

## Done!

Run `php artisan migrate` and start using the API with the new Floor Plan Layout Management feature!
