<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaptopServiceController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard (will trigger login if guest)
Route::redirect('/', '/dashboard');

// Guest only routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [LaptopServiceController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Laptop Service CRUD actions
    Route::post('/services', [LaptopServiceController::class, 'store'])->name('services.store');
    Route::put('/services/{laptopService}', [LaptopServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{laptopService}', [LaptopServiceController::class, 'destroy'])->name('services.destroy');
});

