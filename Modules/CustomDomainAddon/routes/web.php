<?php

use Illuminate\Support\Facades\Route;
use Modules\CustomDomainAddon\App\Http\Controllers\Business\DomainController;
use Modules\CustomDomainAddon\App\Http\Controllers\Admin\AcnooDomainController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth', 'admin']], function () {
    Route::resource('domains', AcnooDomainController::class)->only('index', 'edit', 'update', 'destroy');
    Route::post('domains/status/{id}', [AcnooDomainController::class, 'status'])->name('domains.status');
    Route::post('domains/delete-all', [AcnooDomainController::class, 'deleteAll'])->name('domains.delete-all');
    Route::post('domains/filter', [AcnooDomainController::class, 'acnooFilter'])->name('domains.filter');
    Route::get('domains/instructions', [AcnooDomainController::class, 'instructions'])->name('domains.instructions');
    Route::post('domains/reject/{id}', [AcnooDomainController::class, 'reject'])->name('domains.reject');
    Route::post('domains/approved/{id}', [AcnooDomainController::class, 'approved'])->name('domains.approved');
});

Route::group(['as' => 'business.', 'prefix' => 'business', 'middleware' => ['users', 'expired']], function () {
    Route::resource('domains', DomainController::class)->only('index', 'store', 'destroy');
    Route::post('domains/filter', [DomainController::class, 'acnooFilter'])->name('domains.filter');
    Route::post('domains/delete-all', [DomainController::class, 'deleteAll'])->name('domains.delete-all');
});
