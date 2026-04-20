<?php

use Illuminate\Support\Facades\Route;

// Import all your controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\MouvementController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommandController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\ArchiveController;
use App\Http\Controllers\ReportController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
// Swagger Documentation
Route::get('/documentation', function () {
    return response()->file(storage_path('api-docs/swagger.json'));
});

Route::get('/docs', function () {
    return view('swagger-ui');
})->name('swagger.ui');

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
    Route::get('/commands/{id}/tracking', [CommandController::class, 'tracking']);
    Route::post('/commands', [CommandController::class, 'store']);
    Route::put('/commands/{id}', [CommandController::class, 'update']);
    Route::post('/commands/{id}/cancel', [CommandController::class, 'cancel']);

    // Supplier CRUD Routes (accessible to authenticated users)
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
    
    // Supplier Workflow Endpoints (FS8)
    Route::put('/suppliers/{supplierId}/commands/{commandId}', [SupplierController::class, 'updateCommandStatus']);
    Route::post('/suppliers/{supplierId}/commands/{commandId}/receive', [SupplierController::class, 'receiveCommand']);
    Route::post('/suppliers/{supplierId}/commands/{commandId}/ship', [SupplierController::class, 'sendDelivery']);
    Route::post('/suppliers/{supplierId}/commands/{commandId}/confirm', [SupplierController::class, 'confirmDelivery']);

    /*
    |-- Magasinier Specific Routes --|
    */
    Route::middleware('magasinier')->group(function () {
        // Categories
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories/{id}', [CategoryController::class, 'show']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

        // Warehouses (now includes DELETE)
        Route::get('/warehouses', [WarehouseController::class, 'index']);
        Route::post('/warehouses', [WarehouseController::class, 'store']);
        Route::get('/warehouses/{id}', [WarehouseController::class, 'show']);
        Route::put('/warehouses/{id}', [WarehouseController::class, 'update']);
        Route::delete('/warehouses/{id}', [WarehouseController::class, 'destroy']);

        // Mouvements
        Route::get('/mouvements', [MouvementController::class, 'index']);
        Route::post('/mouvements', [MouvementController::class, 'store']);
        Route::get('/mouvements/{id}', [MouvementController::class, 'show']);
        Route::put('/mouvements/{id}', [MouvementController::class, 'update']);

        // Products
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Alerts
        Route::get('/alerts', [AlertController::class, 'index']);
        Route::get('/alerts/active/count', [AlertController::class, 'getActiveCount']);
        Route::get('/alerts/low-stock/list', [AlertController::class, 'getLowStockAlerts']);
        Route::get('/alerts/{id}', [AlertController::class, 'show']);
        Route::put('/alerts/{id}', [AlertController::class, 'update']);
        Route::delete('/alerts/{id}', [AlertController::class, 'destroy']);

        // Archives
        Route::get('/archives', [ArchiveController::class, 'index']);
        Route::get('/archives/{id}', [ArchiveController::class, 'show']);
        Route::post('/archives', [ArchiveController::class, 'store']);

        // Confirm delivery (Magasinier receives shipment from supplier)
        Route::put('/supplier-deliveries/{supplierId}/commands/{commandId}/confirm', [SupplierController::class, 'confirmDelivery']);
    });

    /*
    |-- Supplier Specific Routes (FS8: receiveCommand, sendDelivery) --|
    */
    Route::middleware('supplier')->group(function () {
        // Supplier sees commands assigned to them
        Route::get('/my-commands', [SupplierController::class, 'receiveCommand']);
        // Supplier marks a command as shipped
        Route::put('/my-commands/{commandId}/ship', [SupplierController::class, 'sendDelivery']);
    });

    /*
    |-- Admin Specific Routes --|
    */
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'index']);
        Route::apiResource('/users', UserController::class);
        
        // Chart Data Endpoints
        Route::get('/reports/movement-chart', [ReportController::class, 'getMovementChartData']);
        Route::get('/reports/category-chart', [ReportController::class, 'getCategoryChartData']);
        Route::get('/reports/alerts-chart', [ReportController::class, 'getAlertsChartData']);
        
        // Report Export Endpoints
        Route::get('/reports/export/products', [ReportController::class, 'exportProductsCSV']);
        Route::get('/reports/export/movements', [ReportController::class, 'exportMovementsCSV']);
        Route::get('/reports/export/commands', [ReportController::class, 'exportCommandsCSV']);
        Route::get('/reports/export/inventory-summary', [ReportController::class, 'exportInventorySummary']);
    });

});