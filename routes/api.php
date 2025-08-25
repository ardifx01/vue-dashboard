<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Test route for debugging
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'timestamp' => now(),
        'environment' => app()->environment()
    ]);
});

// Public Dashboard API routes
Route::prefix('dashboard')->group(function () {
    Route::get('/stats', [DashboardController::class, 'stats']);
    Route::get('/analytics', [DashboardController::class, 'analytics']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // User management
    Route::apiResource('users', UserController::class);
    
    // Role and permission management
    Route::apiResource('roles', RoleController::class);
    Route::get('permissions', [RoleController::class, 'permissions']);
});
