<?php

use App\Http\Controllers\SalesController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Bus;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// excel upload

Route::get('/upload', [SalesController::class, 'index'])->name('excel_upload');

Route::post('/upload', [SalesController::class, 'upload']);

// get real time batch information about rael time job completeion like progress bar
Route::get('/batch/{batchId}', [SalesController::class, 'batch']);

Route::get('/batch-in-progress', [SalesController::class, 'batchInProgress']);


// file upload

Route::get('/file-upload', function () {
    return view('file-upload');
})->name('upload.file');

Route::post('/upload-chunk', [FileController::class, 'uploadChunk'])->name('upload.chunk');

