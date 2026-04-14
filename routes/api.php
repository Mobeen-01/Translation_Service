<?php

use App\Http\Controllers\Api\{AuthController, TranslationController, ExportController, LocaleController};
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/translations/search', [TranslationController::class, 'search']);
    Route::post('/translations/bulk', [TranslationController::class, 'bulk']);
    Route::patch('/translations/{translation}/approve', [TranslationController::class, 'approve']);
    Route::get('/export/{locale}', [ExportController::class, 'export']);

    Route::apiResource('translations', TranslationController::class);
    Route::apiResource('locales', LocaleController::class); 
});