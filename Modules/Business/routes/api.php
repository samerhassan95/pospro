<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Business\App\Http\Controllers\{AcnooRestaurantTableController, AcnooTableReservationController, AcnooTableOrderController, AcnooFloorPlanLayoutController};

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/


Route::middleware(['web', 'auth'])->prefix('business')->group(function () {
    Route::apiResource('tables', AcnooRestaurantTableController::class);
    Route::post('tables/{table}/position', [AcnooRestaurantTableController::class, 'updatePosition']);
    Route::post('tables/{table}/rotate', [AcnooRestaurantTableController::class, 'rotate']);
    
    // Reservation custom actions
    Route::post('reservations/{reservation}/cancel', [AcnooTableReservationController::class, 'cancel']);
    Route::post('reservations/{reservation}/complete', [AcnooTableReservationController::class, 'complete']);
    Route::post('reservations/{reservation}/arrived', [AcnooTableReservationController::class, 'guestArrived']);
    
    Route::apiResource('reservations', AcnooTableReservationController::class);
    Route::post('table-orders/{order}/complete', [AcnooTableOrderController::class, 'complete']);
    Route::apiResource('table-orders', AcnooTableOrderController::class);
    Route::get('floor-layouts/active', [AcnooFloorPlanLayoutController::class, 'getActive']);
    Route::get('floor-layouts/default', [AcnooFloorPlanLayoutController::class, 'getDefault']);
    Route::post('floor-layouts/{layout}/activate', [AcnooFloorPlanLayoutController::class, 'activate']);
    Route::post('floor-layouts/{layout}/set-default', [AcnooFloorPlanLayoutController::class, 'setDefault']);
    Route::post('floor-layouts/{layout}/duplicate', [AcnooFloorPlanLayoutController::class, 'duplicate']);
    Route::apiResource('floor-layouts', AcnooFloorPlanLayoutController::class);
});

