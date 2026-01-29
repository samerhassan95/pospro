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
            $table->integer('position_top')->nullable();
            $table->integer('position_left')->nullable();
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
    protected $fillable = ['business_id', 'table_name', 'table_type', 'chair_count', 'position_top', 'position_left', 'status', 'is_custom', 'is_active'];
    protected $casts = ['is_custom' => 'boolean', 'is_active' => 'boolean'];
    
    public function business() { return $this->belongsTo(Business::class); }
    public function activeReservation() { return $this->hasOne(TableReservation::class, 'table_id')->where('status', 'reserved')->latest(); }
    public function activeOrder() { return $this->hasOne(TableOrder::class, 'table_id')->where('status', 'in_progress')->latest(); }
    public function scopeActive($query) { return $query->where('is_active', true); }
    public function updateStatus($status) { return $this->update(['status' => $status]); }
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
        $data = $request->validate(['table_name' => 'required|string|max:50', 'table_type' => 'required', 'chair_count' => 'required|integer', 'position_top' => 'nullable|integer', 'position_left' => 'nullable|integer']);
        $data['business_id'] = Auth::user()->business_id;
        $data['is_custom'] = true;
        return response()->json(['success' => true, 'data' => RestaurantTable::create($data)], 201);
    }
    
    public function update(Request $request, RestaurantTable $table) {
        if ($table->business_id !== Auth::user()->business_id) return response()->json(['success' => false], 403);
        $table->update($request->all());
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
```

---

## Done!

Run `php artisan migrate` and start using the API.
