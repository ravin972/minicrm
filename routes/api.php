<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    // Protected authentication routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    });
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    
    // Dashboard routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'api']);
        Route::get('/statistics', [DashboardController::class, 'getStatistics']);
        Route::get('/recent-activities', [DashboardController::class, 'getRecentActivities']);
        Route::get('/chart-data', [DashboardController::class, 'getChartData']);
        Route::get('/upcoming-tasks', [DashboardController::class, 'getUpcomingTasks']);
        Route::get('/recent-leads', [DashboardController::class, 'getRecentLeads']);
    });

    // Customer routes
    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/statistics', [CustomerController::class, 'statistics']);
        Route::get('/search', [CustomerController::class, 'search']);
        Route::get('/{customer}', [CustomerController::class, 'show']);
        Route::put('/{customer}', [CustomerController::class, 'update']);
        Route::delete('/{customer}', [CustomerController::class, 'destroy']);
    });

    // Lead routes
    Route::prefix('leads')->group(function () {
        Route::get('/', [LeadController::class, 'index']);
        Route::post('/', [LeadController::class, 'store']);
        Route::get('/statistics', [LeadController::class, 'statistics']);
        Route::get('/my-leads', [LeadController::class, 'myLeads']);
        Route::get('/customer/{customer}', [LeadController::class, 'byCustomer']);
        Route::get('/{lead}', [LeadController::class, 'show']);
        Route::put('/{lead}', [LeadController::class, 'update']);
        Route::patch('/{lead}/status', [LeadController::class, 'updateStatus']);
        Route::delete('/{lead}', [LeadController::class, 'destroy']);
    });

    // Task routes
    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index']);
        Route::post('/', [TaskController::class, 'store']);
        Route::get('/export', [TaskController::class, 'export']);
        Route::get('/statistics', [TaskController::class, 'statistics']);
        Route::get('/my-tasks', [TaskController::class, 'myTasks']);
        Route::get('/upcoming', [TaskController::class, 'upcoming']);
        Route::get('/overdue', [TaskController::class, 'overdue']);
        Route::get('/lead/{lead}', [TaskController::class, 'byLead']);
        Route::get('/{task}', [TaskController::class, 'show']);
        Route::put('/{task}', [TaskController::class, 'update']);
        Route::patch('/{task}/status', [TaskController::class, 'updateStatus']);
        Route::delete('/{task}', [TaskController::class, 'destroy']);
    });
});

// Fallback route for API
Route::fallback(function () {
    return response()->json([
        'success' => false,
        'message' => 'API endpoint not found'
    ], 404);
});
