<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\StockController;
use App\Http\Controllers\QrCodeController;

// Routes pour la gestion des stocks
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/stocks', [StockController::class, 'index']);
    Route::get('/stocks/{stockItem}', [StockController::class, 'show']);
    Route::put('/stocks/{stockItem}', [StockController::class, 'update']);
    Route::post('/stocks/transfer', [StockController::class, 'transfer']);
    Route::get('/stock-movements', [StockController::class, 'getMovements']);
    Route::post('/qrcodes/generate', [QrCodeController::class, 'generate']);
});
