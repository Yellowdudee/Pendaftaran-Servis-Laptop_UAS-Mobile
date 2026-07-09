<?php

// 1. Pastikan semua use statement mengarah ke folder Api
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LaptopServiceController;
use Illuminate\Support\Facades\Route;

// Rute Publik (Murni API JSON)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute Terproteksi (Wajib menyertakan Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Endpoint CRUD data servis laptop murni API JSON untuk Flutter
    Route::get('/services', [LaptopServiceController::class, 'index']);
    Route::post('/services', [LaptopServiceController::class, 'store']);
    
    // Jika kamu ingin update & delete bisa diakses dari mobile nanti, buat juga fungsinya di Api/LaptopServiceController
    Route::put('/services/{id}', [LaptopServiceController::class, 'update']);
    Route::delete('/services/{id}', [LaptopServiceController::class, 'destroy']);
});