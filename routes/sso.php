<?php

use App\Http\Controllers\SSOController;
use Illuminate\Support\Facades\Route;

Route::prefix('sso')->name('sso.')->group(function () {
    Route::get('/login', [SSOController::class, 'login'])->name('login');
    Route::post('/login', [SSOController::class, 'login']);
    Route::get('/auth', [SSOController::class, 'auth'])->name('auth'); // JWT endpoint
    Route::post('/auth', [SSOController::class, 'auth']);
    Route::get('/logout', [SSOController::class, 'logout'])->name('logout');
});
