<?php

use App\Http\Controllers\SalesController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Bus;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', [SalesController::class, 'index']);

Route::post('/upload', [SalesController::class, 'upload']);

// get real time batch information about rael time job completeion like progress bar
Route::get('/batch/{batchId}', [SalesController::class, 'batch']);
