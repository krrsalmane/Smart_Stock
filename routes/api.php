<?php

use Illuminate\Support\Facades\Route;

// Import all your controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\MouvementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// Route::get('/', [PublicController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires JWT)
|--------------------------------------------------------------------------
*/
Route::middleware('jwt')->group(function () {
    
    // Common User Routes
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::put('/user', [AuthController::class, 'updateUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Routes for Clients (General Authenticated Users)
    Route::get('/commands', [CommandController::class, 'index']);
    Route::get('/commands/{id}', [CommandController::class, 'show']);
    Route::post('/commands', [CommandController::class, 'store']);
    Route::put('/commands/{id}', [CommandController::class, 'update']);

    // Supplier Routes (accessible to authenticated users)
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::get('/suppliers/{id}', [SupplierController::class, 'show']);
    Route::post('/suppliers', [SupplierController::class, 'store']);
    Route::put('/suppliers/{id}', [SupplierController::class, 'update']);
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy']);
    
    // Supplier-Product & Supplier-Command Relationships
    Route::post('/suppliers/{id}/products', [SupplierController::class, 'attachProduct']);
    Route::delete('/suppliers/{id}/products/{productId}', [SupplierController::class, 'detachProduct']);
    Route::post('/suppliers/{id}/commands', [SupplierController::class, 'attachCommand']);
    Route::delete('/suppliers/{id}/commands/{commandId}', [SupplierController::class, 'detachCommand']);

  
        // Warehouses
        Route::get('/warehouses', [WarehouseController::class, 'index']);
        Route::post('/warehouses', [WarehouseController::class, 'store']);
        Route::get('/warehouses/{id}', [WarehouseController::class, 'show']);
        Route::put('/warehouses/{id}', [WarehouseController::class, 'update']);
        
       // Route::post('/mouvements', [MouvementController::class, 'store']);

        
        Route::get('/mouvements', [MouvementController::class, 'index']);
        Route::post('/mouvements', [MouvementController::class, 'store']);
        Route::get('/mouvements/{id}', [MouvementController::class, 'show']);
        Route::put('/mouvements/{id}', [MouvementController::class, 'update']);
    });

    /*
    |-- Admin Specific Routes --|
    */
    Route::middleware('admin')->group(function () {
        // Route::get('/admin/dashboard', [AdminController::class, 'index']);
        // Route::apiResource('/users', UserController::class);
    });

    Route::middleware(['jwt', 'magasinier'])->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
    });
});