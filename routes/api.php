<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

// Public routes (tidak perlu auth)
Route::post('/register', [AuthController::class, 'register']); // Registrasi: role selalu 'user'
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']); // Scaffolding untuk email service

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/vehicles', [VehicleController::class, 'index']);
Route::get('/facilities', [FacilityController::class, 'index']);

// Protected routes (requires Sanctum auth)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Bookings - User dapat membuat, melihat, update miliknya; Admin dapat manage semua
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);
    Route::put('/bookings/{booking}', [BookingController::class, 'update']);
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy']);
    Route::patch('/bookings/{booking}/status', [BookingController::class, 'updateStatus']); // Admin only

    // Vehicles - Admin only untuk CRUD
    Route::post('/vehicles', [VehicleController::class, 'store']);
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']);
    Route::put('/vehicles/{vehicle}', [VehicleController::class, 'update']);
    Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy']);

    // Facilities - Admin only untuk CRUD
    Route::post('/facilities', [FacilityController::class, 'store']);
    Route::get('/facilities/{facility}', [FacilityController::class, 'show']);
    Route::put('/facilities/{facility}', [FacilityController::class, 'update']);
    Route::delete('/facilities/{facility}', [FacilityController::class, 'destroy']);
});
