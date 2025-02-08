<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\SalesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/upload', [SalesController::class, 'index']);

Route::post('/upload', [SalesController::class, 'upload']);

// get real time batch information about rael time job completeion like progress bar
Route::get('/batch/{batchId}', [SalesController::class, 'batch']);

Route::get('/batch-in-progress', [SalesController::class, 'batchInProgress']);

// file upload 

Route::prefix('file')->group(function () {
    Route::post('/upload-chunk', [FileController::class, 'uploadChunk']);
});
