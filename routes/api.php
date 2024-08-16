<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('login', [AuthController::class, 'login']);

Route::prefix('verifications')->middleware('auth:sanctum')->group(function () {
    Route::post('store', [VerificationController::class, 'store']);
});
